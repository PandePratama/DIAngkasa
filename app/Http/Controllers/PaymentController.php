<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Transaction;
use App\Models\PurchaseType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $cart = Cart::with(['items.productDiamart.primaryImage', 'items.productDiraditya.primaryImage'])
            ->where('user_id', $user->id)
            ->first();

        if (!$cart) {
            return redirect()->route('cart.index');
        }

        // 1. Hitung Subtotal
        $subtotal = $cart->items->sum(function ($item) {
            $product = $item->id_product_diamart ? $item->productDiamart : $item->productDiraditya;
            return $item->qty * ($product->price ?? 0);
        });

        // 2. Hitung Admin Fee
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
        // 1. Validasi Input
        $request->validate([
            'payment_method' => 'required|exists:purchase_types,code',
        ]);

        $user = auth()->user();

        // 2. Ambil Cart
        $cart = Cart::with(['items.productDiamart', 'items.productDiraditya'])
            ->where('user_id', $user->id)->first();

        if (!$cart) {
            return redirect()->back()->with('error', 'Keranjang belanja tidak ditemukan.');
        }

        // 3. Ambil Data Tipe Pembayaran
        $purchaseType = PurchaseType::where('code', $request->payment_method)->first();
        if (!$purchaseType) {
            return redirect()->back()->with('error', 'Metode pembayaran tidak valid.');
        }

        // 4. Hitung Total Akhir
        $subtotal = $cart->items->sum(function ($item) {
            $product = $item->id_product_diamart ? $item->productDiamart : $item->productDiraditya;
            return $item->qty * ($product->price ?? 0);
        });

        $adminFee = ($cart->business_unit == 'diamart') ? 0 : 20000;
        $grandTotal = $subtotal + $adminFee;

        // --- LOGIKA STATUS & SNAPSHOT SALDO ---

        $currentBalance = $user->saldo;
        $balanceSnapshot = $currentBalance;

        // Default Status (Cash)
        $transactionStatus = 'ongoing';
        $paymentStatus = 'unpaid';

        // KHUSUS BALANCE (POTONG SALDO)
        if ($purchaseType->code == 'balance') {
            // Cek Saldo
            if ($user->saldo < $grandTotal) {
                return redirect()->back()->with('error', 'Saldo tidak mencukupi. Sisa saldo: Rp ' . number_format($user->saldo));
            }

            // Hitung sisa saldo SETELAH transaksi (Snapshot)
            $balanceSnapshot = $currentBalance - $grandTotal;

            // Set status jadi selesai
            $transactionStatus = 'completed';
            $paymentStatus = 'paid';
        }

        // --- MULAI PROSES TRANSAKSI ---
        try {
            DB::beginTransaction();

            // 1. Simpan Transaksi
            $trx = Transaction::create([
                'user_id'          => $user->id,
                'invoice_code'     => 'INV-' . time() . rand(100, 999),
                'grand_total'      => $grandTotal,
                'purchase_type_id' => $purchaseType->id,
                'payment_method'   => $purchaseType->code, // Pastikan view success.blade.php membaca kolom ini
                'status'           => $transactionStatus,
                'payment_status'   => $paymentStatus,
                'balance_after'    => $balanceSnapshot,
            ]);

            // 2. EKSEKUSI POTONG SALDO (Jika Balance)
            if ($purchaseType->code == 'balance') {
                $user->decrement('saldo', $grandTotal);
                // Auth::user()->refresh() tidak wajib di sini karena request akan berakhir/redirect
            }

            // 3. KURANGI STOK
            foreach ($cart->items as $item) {
                $product = $item->id_product_diamart ? $item->productDiamart : $item->productDiraditya;

                if ($product->stock < $item->qty) {
                    throw new \Exception("Stok produk {$product->name} tidak mencukupi.");
                }
                $product->decrement('stock', $item->qty);
            }

            // 4. BERSIHKAN KERANJANG
            $cart->items()->delete();
            $cart->delete();

            DB::commit();

            // --- PERUBAHAN DI SINI (REDIRECT) ---

            // Kita hapus logika "If Cash -> Index".
            // Sekarang SEMUA metode (Cash & Balance) masuk ke halaman Success.
            // Biarkan file success.blade.php yang mengatur tampilan teks (Kuning/Hijau).

            return redirect()->route('transaction.success');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Payment Error: " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memproses transaksi: ' . $e->getMessage());
        }
    }
}
