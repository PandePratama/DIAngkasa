<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CreditInstallment;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\PurchaseType;
use App\Models\CreditTransaction;
use App\Models\BalanceMutation;
use App\Services\CreditCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon; // <--- PENTING: Untuk hitung tanggal

class PaymentController extends Controller
{
    /**
     * MENAMPILKAN HALAMAN CHECKOUT (INDEX)
     */
    public function index()
    {
        $user = auth()->user();

        // Load keranjang belanja
        $cart = Cart::with(['items.productDiamart.primaryImage', 'items.productDiraditya.primaryImage'])
            ->where('id_user', $user->id)
            ->first();

        // Jika keranjang kosong
        if (!$cart) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja kosong.');
        }

        // Hitung Subtotal
        $subtotal = $cart->items->sum(function ($item) {
            $product = $item->id_product_diamart ? $item->productDiamart : $item->productDiraditya;
            return $item->qty * ($product->price ?? 0);
        });

        // Tentukan Admin Fee
        if ($cart->business_unit == 'diamart') {
            $adminFee = 0;
            $adminLabel = "Biaya Layanan";
        } else {
            $adminFee = 20000;
            $adminLabel = "Biaya Admin";
        }

        $total = $subtotal + $adminFee;

        return view('payment.index', compact('cart', 'subtotal', 'adminFee', 'adminLabel', 'total'));
    }

    /**
     * MEMPROSES PEMBAYARAN (ROUTER UTAMA)
     */
    public function process(Request $request)
    {
        try {
            $user = auth()->user();

            $request->validate([
                'payment_method' => 'required',
            ]);

            $cart = Cart::with(['items.productDiamart', 'items.productDiraditya'])
                ->where('id_user', $user->id)->first();

            if (!$cart) {
                return redirect()->back()->with('error', 'Keranjang belanja tidak ditemukan.');
            }

            // Router ke logic masing-masing
            if ($request->payment_method === 'credit') {
                $creditService = app(CreditCalculatorService::class);
                return $this->processCreditPayment($request, $cart, $user, $creditService);
            } else {
                return $this->processRegularPayment($request, $cart, $user);
            }
        } catch (\Exception $e) {
            Log::error("Payment Error: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi Kesalahan Sistem: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * LOGIKA KREDIT (Dengan Due Date & Mutasi Saldo Akurat)
     */
    private function processCreditPayment($request, $cart, $user, $creditService)
    {
        // 1. Validasi Input
        $request->validate([
            'tenor' => 'required|in:3,6,9,12',
            'dp_amount' => 'required|numeric|min:0'
        ]);

        // 2. Validasi Bisnis
        if ($cart->business_unit !== 'raditya') {
            throw new \Exception('Kredit hanya untuk produk Gadget.');
        }
        if ($cart->items->count() > 1) {
            throw new \Exception('Kredit hanya bisa 1 item per transaksi.');
        }

        $item = $cart->items->first();
        $product = $item->productDiraditya;

        if (!$product) throw new \Exception('Produk tidak ditemukan.');
        if ($product->stock < $item->qty) throw new \Exception("Stok {$product->name} kurang.");

        // 3. Hitung Kalkulasi Kredit
        $calc = $creditService->calculate($product, $request->tenor, $request->dp_amount);

        // 4. Cek Saldo Cukup Buat DP?
        if ($user->saldo < $calc['dp_amount']) {
            throw new \Exception('Saldo kurang untuk bayar DP.');
        }

        DB::beginTransaction();
        try {
            // A. Simpan Header Kredit
            $creditTrx = CreditTransaction::create([
                'id_product' => $product->id,
                'id_user' => $user->id,
                'tenor' => $calc['tenor'],
                'admin_fee' => 20000,
                'up_price' => $calc['up_price_percent'],
                'monthly_amount' => $calc['monthly_installment'],
                'status' => 'progress',
                'total_paid_month' => 0,
                'dp_amount' => $calc['dp_amount'], // Pastikan Model guarded=['id']
            ]);

            // B. Simpan Jadwal Cicilan (Dengan Due Date)
            $schedules = $creditService->generateSchedule($calc);
            foreach ($schedules as $sch) {

                // Hitung Tanggal Jatuh Tempo (Tanggal 25 Bulan Berikutnya)
                $jatuhTempo = Carbon::now()
                    ->addMonths($sch['month_sequence'])
                    ->setDay(25);

                \App\Models\CreditInstallment::create([
                    'id_credit_transaction' => $creditTrx->id,
                    'id_user'               => $user->id,
                    'installment_month'     => $sch['month_sequence'],
                    'amount'                => abs($sch['amount']),

                    // --- UPDATE: Simpan Tanggal Jatuh Tempo ---
                    'due_date'              => $jatuhTempo->format('Y-m-d'),
                    // ------------------------------------------

                    'admin_fee'             => 0,
                    'balance_before'        => 0,
                    'balance_after'         => 0,
                ]);
            }

            // C. POTONG SALDO & CATAT MUTASI
            $saldoAwal = $user->saldo;
            $potonganDP = $calc['dp_amount'];
            $saldoAkhir = $saldoAwal - $potonganDP;

            // 1. Eksekusi Potong Saldo di DB
            $user->decrement('saldo', $potonganDP);

            // 2. Catat Mutasi (Gunakan variabel $saldoAkhir yang kita hitung manual agar Realtime)
            BalanceMutation::create([
                'user_id' => $user->id,
                'type'    => 'debit',
                'amount'  => $potonganDP,
                'current_balance' => $saldoAkhir,
                'description' => "Pembayaran DP Kredit: {$product->name}",
                'reference_id' => 'CREDIT-' . $creditTrx->id
            ]);

            // D. Update Stok & Hapus Cart
            $product->decrement('stock', $item->qty);
            $cart->items()->delete();
            $cart->delete();

            DB::commit();

            return redirect()->route('transaction.success')
                ->with('credit_trx_id', $creditTrx->id);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * LOGIKA REGULER (CASH/SALDO)
     */
    private function processRegularPayment($request, $cart, $user)
    {
        $purchaseType = PurchaseType::where('code', $request->payment_method)->first();
        if (!$purchaseType) throw new \Exception("Metode pembayaran invalid.");

        $subtotal = $cart->items->sum(function ($item) {
            $product = $item->id_product_diamart ? $item->productDiamart : $item->productDiraditya;
            return $item->qty * ($product->price ?? 0);
        });

        $adminFee = ($cart->business_unit == 'diamart') ? 0 : 20000;
        $grandTotal = $subtotal + $adminFee;

        $balanceSnapshot = $user->saldo;
        $status = 'ongoing';
        $paymentStatus = 'unpaid';

        // Cek Saldo
        if ($purchaseType->code == 'balance') {
            if ($user->saldo < $grandTotal) {
                throw new \Exception("Saldo tidak cukup.");
            }
            $balanceSnapshot -= $grandTotal;
            $status = 'completed';
            $paymentStatus = 'paid';
        }

        DB::beginTransaction();
        try {
            // 1. Buat Transaksi
            $trx = Transaction::create([
                'id_user' => $user->id,
                'invoice_code' => 'INV-' . time() . rand(100, 999),
                'grand_total' => $grandTotal,
                'purchase_type_id' => $purchaseType->id,
                'payment_method' => $purchaseType->code,
                'status' => $status,
                'payment_status' => $paymentStatus,
                'balance_after' => $balanceSnapshot,
            ]);

            // 2. Simpan Item
            foreach ($cart->items as $item) {
                $product = $item->id_product_diamart ? $item->productDiamart : $item->productDiraditya;
                TransactionItem::create([
                    'transaction_id' => $trx->id,
                    'id_product_diamart' => $item->id_product_diamart,
                    'id_product_diraditya' => $item->id_product_diraditya,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'qty' => $item->qty,
                    'subtotal' => $product->price * $item->qty,
                ]);

                if ($product->stock < $item->qty) throw new \Exception("Stok {$product->name} habis.");
                $product->decrement('stock', $item->qty);
            }

            // 3. POTONG SALDO & CATAT MUTASI (Jika pakai Saldo)
            if ($purchaseType->code == 'balance') {
                $user->decrement('saldo', $grandTotal);

                // Catat Mutasi
                BalanceMutation::create([
                    'user_id' => $user->id,
                    'type'    => 'debit',
                    'amount'  => $grandTotal,
                    'current_balance' => $balanceSnapshot, // Sudah dihitung diatas
                    'description' => "Pembayaran Belanja: {$trx->invoice_code}",
                    'reference_id' => 'TRX-' . $trx->id
                ]);
            }

            $cart->items()->delete();
            $cart->delete();

            DB::commit();
            return redirect()->route('transaction.success');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
