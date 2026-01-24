<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Installment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // Tambahkan ini
use Illuminate\Support\Facades\Log;  // Tambahkan ini

class PaymentController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Pastikan load relasi produk dengan benar
        $cart = Cart::with(['items.productDiamart.primaryImage', 'items.productDiraditya.primaryImage'])
            ->where('user_id', $user->id)
            ->first();

        if (!$cart) {
            return redirect()->route('cart.index');
        }

        $cartItems = $cart->items;

        // 1. Hitung Subtotal
        $subtotal = $cartItems->sum(function ($item) {
            $product = $item->id_product_diamart ? $item->productDiamart : $item->productDiraditya;
            return $item->qty * ($product->price ?? 0);
        });

        // 2. Logika Admin Fee
        if ($cart->business_unit == 'diamart') {
            $adminFee = 0;
            $adminLabel = "Biaya Layanan (1%)"; // Label saja, nilai 0
        } else {
            $adminFee = 20000;
            $adminLabel = "Biaya Admin";
        }

        $total = $subtotal + $adminFee;

        return view('payment.index', compact('cart', 'cartItems', 'subtotal', 'adminFee', 'adminLabel', 'total'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,credit',
            'tenure' => 'nullable|integer'
        ]);

        $user = auth()->user();

        // Ambil Cart
        $cart = Cart::with(['items.productDiamart', 'items.productDiraditya'])
            ->where('user_id', $user->id)->first();

        if (!$cart) {
            return redirect()->back()->with('error', 'Keranjang belanja tidak ditemukan.');
        }

        // Logic Admin Fee
        $adminFee = ($cart->business_unit == 'diamart') ? 0 : 20000;

        // Hitung Subtotal
        $subtotal = $cart->items->sum(function ($item) {
            $product = $item->id_product_diamart ? $item->productDiamart : $item->productDiraditya;
            return $item->qty * ($product->price ?? 0);
        });

        // Tentukan Cicilan
        $tenure = ($request->payment_method == 'credit') ? ($request->tenure ?? 3) : 1;
        $monthlyPrincipal = $subtotal / $tenure;

        // Tagihan Pertama
        $firstBill = $monthlyPrincipal + $adminFee;

        // Cek Saldo
        if ($user->saldo < $firstBill) {
            return redirect()->back()->with('error', 'Saldo tidak mencukupi. Sisa saldo: Rp ' . number_format($user->saldo));
        }

        // --- MULAI PROSES TRANSAKSI ---
        try {
            DB::beginTransaction();

            // 1. Simpan Transaksi
            $trx = Transaction::create([
                'user_id'      => $user->id,
                'invoice_code' => 'INV-' . time() . rand(100, 999),
                'grand_total'  => $subtotal + $adminFee,
                'payment_type' => $request->payment_method,
                'tenure'       => $tenure,
            ]);

            // 2. Simpan Cicilan
            for ($i = 1; $i <= $tenure; $i++) {
                $amount = ($i == 1) ? $firstBill : $monthlyPrincipal;
                Installment::create([
                    'id_order'           => $trx->id, // Pastikan kolom ini benar di DB (id_order atau transaction_id)
                    'installment_number' => $i,
                    'amount'             => $amount,
                    'due_date'           => now()->addMonths($i - 1),
                    'status'             => ($i == 1) ? 'paid' : 'pending',
                ]);
            }

            // 3. POTONG SALDO (UPDATE DATABASE)
            // Ini akan langsung mengubah angka di database
            $user->decrement('saldo', $firstBill);

            // 4. Kurangi Stok
            foreach ($cart->items as $item) {
                $product = $item->id_product_diamart ? $item->productDiamart : $item->productDiraditya;
                if ($product->stock < $item->qty) {
                    throw new \Exception("Stok {$product->name} habis.");
                }
                $product->decrement('stock', $item->qty);
            }

            // 5. Hapus Keranjang
            $cart->items()->delete();
            $cart->delete();

            DB::commit(); // Simpan Permanen

            // UPDATE SESSION USER AGAR TIDAK LOGOUT
            // Ini trik pentingnya:
            Auth::user()->refresh();

            return redirect()->route('transaction.success')->with('success', 'Pembayaran Berhasil!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Payment Error: " . $e->getMessage()); // Cek storage/logs/laravel.log jika error
            return redirect()->back()->with('error', 'Gagal memproses: ' . $e->getMessage());
        }
    }
}
