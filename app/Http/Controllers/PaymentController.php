<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CreditInstallment;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\PurchaseType;
use App\Models\CreditTransaction;
use App\Services\CreditCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $cart = Cart::with(['items.productDiamart.primaryImage', 'items.productDiraditya.primaryImage'])
            ->where('id_user', $user->id)
            ->first();

        if (!$cart) return redirect()->route('cart.index')->with('error', 'Keranjang kosong.');

        $subtotal = $cart->items->sum(function ($item) {
            $product = $item->id_product_diamart ? $item->productDiamart : $item->productDiraditya;
            return $item->qty * ($product->price ?? 0);
        });

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

    // --- PERBAIKAN UTAMA DISINI ---
    public function process(Request $request)
    {
        try {
            // 1. Debugging: Cek data yang dikirim frontend
            // dd($request->all());

            $user = auth()->user();

            // 2. Validasi Basic
            $request->validate([
                'payment_method' => 'required',
            ]);

            // 3. Ambil Cart
            $cart = Cart::with(['items.productDiamart', 'items.productDiraditya'])
                ->where('id_user', $user->id)->first();

            if (!$cart) {
                return redirect()->back()->with('error', 'Keranjang belanja tidak ditemukan.');
            }

            // 4. Router Logika Pembayaran
            if ($request->payment_method === 'credit') {
                // Panggil Service secara manual agar lebih aman
                $creditService = app(CreditCalculatorService::class);
                return $this->processCreditPayment($request, $cart, $user, $creditService);
            } else {
                return $this->processRegularPayment($request, $cart, $user);
            }
        } catch (\Exception $e) {
            // Tangkap SEMUA error agar aplikasi tidak crash layar putih
            Log::error("Payment Error: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi Kesalahan Sistem: ' . $e->getMessage())->withInput();
        }
    }

    private function processCreditPayment($request, $cart, $user, $creditService)
    {
        // Validasi Input Kredit
        $request->validate([
            'tenor' => 'required|in:3,6,9,12',
            'dp_amount' => 'required|numeric|min:0'
        ]);

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

        // Hitung
        $calc = $creditService->calculate($product, $request->tenor, $request->dp_amount);

        if ($user->saldo < $calc['dp_amount']) {
            throw new \Exception('Saldo kurang untuk bayar DP.');
        }

        DB::beginTransaction();
        try {
            $creditTrx = CreditTransaction::create([
                // [BENAR] Sesuai Database
                'id_product' => $product->id,
                'id_user' => $user->id,
                'tenor' => $calc['tenor'],
                'admin_fee' => 20000,

                // [PERBAIKAN DISINI] Ubah nama key agar sesuai kolom database:

                // Database: 'up_price' | Data: $calc['up_price_percent']
                'up_price' => $calc['up_price_percent'],

                // Database: 'monthly_amount' | Data: $calc['monthly_installment']
                'monthly_amount' => $calc['monthly_installment'],

                // Database: 'status' (Enum: progress, paid, complete)
                'status' => 'progress',

                // Database: 'total_paid_month' (Default 0)
                'total_paid_month' => 0,

                // [OPSIONAL] Jika kolom ini TIDAK ADA di database credit_transactions Anda, HAPUS baris ini:
                // 'product_hpp_snapshot' => $calc['product_hpp_snapshot'],
                // 'product_price_snapshot' => $calc['product_price_snapshot'],
                // 'retail_price_value' => $calc['retail_price'],
                // 'dp_amount' => $calc['dp_amount'],
            ]);

            $schedules = $creditService->generateSchedule($calc);

            foreach ($schedules as $sch) {

                // Pastikan Model yang dipanggil sesuai nama tabel di pesan error (credit_installment)
                // Jika nama model Anda CreditInstallment, pastikan $table = 'credit_installment' di modelnya.

                \App\Models\CreditInstallment::create([
                    'id_credit_transaction' => $creditTrx->id,

                    // --- PERBAIKAN: TAMBAHKAN BARIS INI ---
                    'id_user'               => $user->id,
                    // --------------------------------------

                    'installment_month'     => $sch['month_sequence'],
                    'amount'                => abs($sch['amount']), // Pastikan nilai positif
                    'admin_fee'             => 0,
                    'balance_before'        => 0,
                    'balance_after'         => 0,
                    // Hapus 'status' jika kolom tersebut tidak ada di tabel ini
                ]);
            }
            $user->decrement('saldo', $calc['dp_amount']);
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

    private function processRegularPayment($request, $cart, $user)
    {
        // Cek Tabel Purchase Type
        $purchaseType = PurchaseType::where('code', $request->payment_method)->first();

        if (!$purchaseType) {
            throw new \Exception("Metode pembayaran '{$request->payment_method}' belum disetting di database (Tabel purchase_types kosong?).");
        }

        $subtotal = $cart->items->sum(function ($item) {
            $product = $item->id_product_diamart ? $item->productDiamart : $item->productDiraditya;
            return $item->qty * ($product->price ?? 0);
        });

        $adminFee = ($cart->business_unit == 'diamart') ? 0 : 20000;
        $grandTotal = $subtotal + $adminFee;

        $balanceSnapshot = $user->saldo;
        $status = 'ongoing';
        $paymentStatus = 'unpaid';

        // Logic Saldo
        if ($purchaseType->code == 'balance') {
            if ($user->saldo < $grandTotal) {
                throw new \Exception("Saldo Anda (Rp " . number_format($user->saldo) . ") tidak cukup untuk membayar tagihan (Rp " . number_format($grandTotal) . ").");
            }
            $balanceSnapshot -= $grandTotal;
            $status = 'completed';
            $paymentStatus = 'paid';
        }

        DB::beginTransaction();
        try {
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

                if ($product->stock < $item->qty) {
                    throw new \Exception("Stok {$product->name} tidak cukup.");
                }
                $product->decrement('stock', $item->qty);
            }

            if ($purchaseType->code == 'balance') {
                $user->decrement('saldo', $grandTotal);
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
