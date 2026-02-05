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
use App\Enums\InstallmentStatus; // Pastikan pakai Enum atau string 'paid'/'unpaid'
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $cart = Cart::with(['items.productDiamart.primaryImage', 'items.productDiraditya.primaryImage'])
            ->where('id_user', $user->id)->first();

        if (!$cart) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja kosong.');
        }

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

    public function process(Request $request)
    {
        try {
            $user = auth()->user();
            $request->validate(['payment_method' => 'required']);

            $cart = Cart::with(['items.productDiamart', 'items.productDiraditya'])
                ->where('id_user', $user->id)->first();

            if (!$cart) return redirect()->back()->with('error', 'Keranjang tidak ditemukan.');

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

    // ==========================================
    // LOGIKA KREDIT BARU (DP 0 = Bayar Angsuran 1)
    // ==========================================
    private function processCreditPayment($request, $cart, $user, $creditService)
    {
        $request->validate([
            'tenor' => 'required|in:3,6,9,12',
            'dp_amount' => 'required|numeric|min:0'
        ]);

        if ($cart->business_unit !== 'raditya') throw new \Exception('Kredit hanya untuk produk Gadget.');
        if ($cart->items->count() > 1) throw new \Exception('Kredit hanya bisa 1 item per transaksi.');

        $item = $cart->items->first();
        $product = $item->productDiraditya;

        if (!$product) throw new \Exception('Produk tidak ditemukan.');
        if ($product->stock < $item->qty) throw new \Exception("Stok {$product->name} kurang.");

        // 1. Kalkulasi Kredit
        $calc = $creditService->calculate($product, $request->tenor, $request->dp_amount);

        // 2. VALIDASI LIMIT KREDIT (Total Pinjaman vs Saldo)
        if ($calc['retail_price'] > $user->saldo) { // Menggunakan Retail Price sebagai acuan limit
            $kurang = $calc['retail_price'] - $user->saldo;
            throw new \Exception("Limit kredit tidak cukup! (Kurang: Rp " . number_format($kurang) . ")");
        }

        // =========================================================
        // 3. TENTUKAN APA YANG HARUS DIBAYAR SEKARANG (NOMINAL AWAL)
        // =========================================================
        $schedules = $creditService->generateSchedule($calc);
        $isNoDp = ($calc['dp_amount'] == 0); // Cek apakah DP 0

        if ($isNoDp) {
            // SKEMA DP 0: Bayar (Cicilan Bulan 1 + Admin Fee 20rb) SEKARANG
            // Ambil nominal bulan ke-1 dari schedule
            $nominalBayarAwal = $schedules[0]['amount'];
            $keteranganMutasi = "Pembayaran Angsuran Pertama (DP 0): {$product->name}";
        } else {
            // SKEMA NORMAL: Bayar DP SEKARANG
            $nominalBayarAwal = $calc['dp_amount'];
            $keteranganMutasi = "Pembayaran DP Kredit: {$product->name}";
        }

        // 4. Cek Saldo Cukup untuk Bayar Awal?
        if ($user->saldo < $nominalBayarAwal) {
            throw new \Exception('Saldo kurang untuk pembayaran awal (Rp ' . number_format($nominalBayarAwal) . ').');
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
                'total_paid_month' => $isNoDp ? 1 : 0, // Jika DP 0, berarti bulan 1 sudah lunas
                'dp_amount' => $calc['dp_amount'],
            ]);

            // B. Simpan Jadwal Cicilan (Looping)
            foreach ($schedules as $sch) {
                $bulanKe = $sch['month_sequence'];
                $statusCicilan = 'unpaid';
                $jatuhTempo = null;

                // --- LOGIC JATUH TEMPO & STATUS ---
                if ($isNoDp) {
                    // KASUS DP 0
                    if ($bulanKe == 1) {
                        // Bulan 1: LUNAS SEKARANG
                        $statusCicilan = 'paid';
                        $jatuhTempo = Carbon::now(); // Jatuh tempo hari ini (sudah dibayar)
                    } else {
                        // Bulan 2 dst: Jatuh tempo maju 1 langkah
                        // Contoh: Beli Feb. Bulan 1 (Feb) Lunas. Bulan 2 Jatuh Tempo Maret.
                        // addMonths($bulanKe - 1) -> Jika bulan 2, maka addMonths(1) = Bulan Depan.
                        $jatuhTempo = Carbon::now()
                            ->addMonths($bulanKe - 1)
                            ->setDay(25);
                    }
                } else {
                    // KASUS NORMAL (ADA DP)
                    // Bulan 1: Jatuh tempo Bulan Depan (addMonths 1)
                    $jatuhTempo = Carbon::now()
                        ->addMonths($bulanKe)
                        ->setDay(25);
                }

                \App\Models\CreditInstallment::create([
                    'id_credit_transaction' => $creditTrx->id,
                    'id_user'               => $user->id,
                    'installment_month'     => $bulanKe,
                    'amount'                => abs($sch['amount']),
                    'due_date'              => $jatuhTempo->format('Y-m-d'),
                    'status'                => $statusCicilan, // paid atau unpaid
                    'admin_fee'             => 0,
                    'balance_before'        => 0,
                    'balance_after'         => 0,
                    'updated_at'            => ($statusCicilan == 'paid') ? now() : null, // Tandai waktu bayar jika paid
                ]);
            }

            // C. POTONG SALDO & CATAT MUTASI
            $saldoAwal = $user->saldo;
            $saldoAkhir = $saldoAwal - $nominalBayarAwal;

            // 1. Eksekusi Potong Saldo
            $user->decrement('saldo', $nominalBayarAwal);

            // 2. Catat Mutasi
            BalanceMutation::create([
                'user_id' => $user->id,
                'type'    => 'debit',
                'amount'  => $nominalBayarAwal,
                'current_balance' => $saldoAkhir,
                'description' => $keteranganMutasi,
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

        if ($purchaseType->code == 'balance') {
            if ($user->saldo < $grandTotal) throw new \Exception("Saldo tidak cukup.");
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

                if ($product->stock < $item->qty) throw new \Exception("Stok {$product->name} habis.");
                $product->decrement('stock', $item->qty);
            }

            if ($purchaseType->code == 'balance') {
                $user->decrement('saldo', $grandTotal);
                BalanceMutation::create([
                    'user_id' => $user->id,
                    'type'    => 'debit',
                    'amount'  => $grandTotal,
                    'current_balance' => $balanceSnapshot,
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
