<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CreditTransaction;
use App\Models\CreditInstallment;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\PurchaseType;
use App\Models\BalanceMutation;
use App\Services\CreditCalculatorService;
use App\Enums\InstallmentStatus;
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
    // LOGIKA KREDIT BARU (DP 0 vs DP > 0)
    // ==========================================
    private function processCreditPayment($request, $cart, $user, $creditService)
    {
        $request->validate([
            'tenor' => 'required|in:3,6,9,12',
            'dp_amount' => 'required' // Numeric validation handled later due to format
        ]);

        if ($cart->business_unit !== 'raditya') throw new \Exception('Kredit hanya untuk produk Gadget.');
        if ($cart->items->count() > 1) throw new \Exception('Kredit hanya bisa 1 item per transaksi.');

        $item = $cart->items->first();
        $product = $item->productDiraditya;

        if (!$product) throw new \Exception('Produk tidak ditemukan.');
        if ($product->stock < $item->qty) throw new \Exception("Stok {$product->name} kurang.");

        // Bersihkan DP dari format ribuan (titik)
        $rawDP = str_replace('.', '', $request->dp_amount);
        $dpAmount = is_numeric($rawDP) ? (float)$rawDP : 0;

        // 1. Kalkulasi Kredit
        $calc = $creditService->calculate($product, $request->tenor, $dpAmount);

        // 2. VALIDASI LIMIT KREDIT (Total Pinjaman vs Saldo)
        // Retail Price adalah total harga barang setelah markup
        // Pastikan limit user cukup untuk menanggung total harga tersebut
        if ($calc['retail_price'] > $user->saldo) {
            $kurang = $calc['retail_price'] - $user->saldo;
            // Opsional: Anda bisa throw exception atau biarkan (tergantung kebijakan limit)
            // throw new \Exception("Limit kredit tidak cukup! (Kurang: Rp " . number_format($kurang) . ")");
        }

        // =========================================================
        // 3. TENTUKAN APA YANG HARUS DIBAYAR SEKARANG (NOMINAL AWAL)
        // =========================================================
        $schedules = $creditService->generateSchedule($calc);
        $isNoDp = ($calc['dp_amount'] <= 0); // Cek apakah DP 0 atau kurang

        $adminFee = 20000; // Biaya Admin tetap

        if ($isNoDp) {
            // SKEMA DP 0:
            // Bayar Sekarang = Cicilan Bulan 1 + Admin Fee (Logic Baru)
            $installmentAmount = $schedules[0]['amount']; // Ambil nominal cicilan pertama

            $nominalBayarAwal = $installmentAmount + $adminFee;
            $keteranganMutasi = "Angsuran Awal (Tanpa DP): {$product->name}";
        } else {
            // SKEMA NORMAL (ADA DP):
            // Bayar Sekarang = DP + Admin Fee
            $nominalBayarAwal = $calc['dp_amount'] + $adminFee; // Tambah admin fee ke DP
            $keteranganMutasi = "Pembayaran DP Kredit: {$product->name}";
        }

        // 4. Cek Saldo Cukup untuk Bayar Awal?
        if ($user->saldo < $nominalBayarAwal) {
            throw new \Exception('Saldo kurang untuk pembayaran awal (Rp ' . number_format($nominalBayarAwal, 0, ',', '.') . ').');
        }

        DB::beginTransaction();
        try {
            // A. Simpan Header Kredit
            $creditTrx = CreditTransaction::create([
                'id_product' => $product->id,
                'id_user' => $user->id,
                'tenor' => $calc['tenor'],
                'admin_fee' => $adminFee,
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
                $updatedAt = now(); // Default creation time

                // --- LOGIC JATUH TEMPO & STATUS ---
                if ($isNoDp) {
                    // KASUS DP 0
                    if ($bulanKe == 1) {
                        // Bulan 1: LUNAS SEKARANG (Status Paid)
                        $statusCicilan = 'paid';
                        $jatuhTempo = Carbon::now(); // Jatuh tempo hari ini
                        $updatedAt = now(); // Timestamp bayar
                    } else {
                        // Bulan 2 dst: Jatuh tempo maju 1 bulan dari sekarang
                        // Karena bulan 1 sudah lunas sekarang, maka bulan 2 jatuh tempo bulan depan.
                        // addMonths($bulanKe - 1) -> Jika bulan 2, maka addMonths(1) = Bulan Depan.
                        $jatuhTempo = Carbon::now()
                            ->addMonths($bulanKe - 1)
                            ->setDay(25);
                        $updatedAt = null; // Belum dibayar
                    }
                } else {
                    // KASUS NORMAL (ADA DP)
                    // Bulan 1: Jatuh tempo Bulan Depan (addMonths 1)
                    $jatuhTempo = Carbon::now()
                        ->addMonths($bulanKe)
                        ->setDay(25);
                    $updatedAt = null; // Belum dibayar
                }

                CreditInstallment::create([
                    'id_credit_transaction' => $creditTrx->id,
                    'id_user'               => $user->id,
                    'installment_month'     => $bulanKe,
                    'amount'                => abs($sch['amount']), // Pastikan positif
                    'due_date'              => $jatuhTempo->format('Y-m-d'),
                    'status'                => $statusCicilan, // paid atau unpaid
                    'admin_fee'             => 0,
                    'balance_before'        => 0,
                    'balance_after'         => 0,
                    'updated_at'            => ($statusCicilan == 'paid') ? now() : $updatedAt,
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

        // Cek Unit Bisnis untuk Admin Fee (Logic Lama)
        $adminFee = ($cart->business_unit == 'diamart') ? 0 : 20000;

        // ============================================================
        // LOGIC BARU: TENTUKAN PREFIX INVOICE
        // ============================================================
        // Jika business_unit 'diamart' pakai 'DIA', selain itu (raditya) pakai 'RDT'
        $invoicePrefix = ($cart->business_unit == 'diamart') ? 'DIA' : 'RDT';
        // ============================================================

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

                // GUNAKAN PREFIX YANG SUDAH DITENTUKAN DI ATAS
                'invoice_code' => $invoicePrefix . '-' . time() . rand(100, 999),

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
