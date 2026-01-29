<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductDiamart;
use App\Models\ProductRaditya;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // --- 1. MENAMPILKAN KERANJANG ---
    public function index()
    {
        $user = Auth::user();

        $carts = Cart::with([
            'items.productDiamart.primaryImage',   // Load data produk sembako + gambar
            'items.productDiraditya.primaryImage'  // Load data produk elektronik + gambar
        ])
            ->where('id_user', $user->id)
            ->get();

        // 2. Hitung Grand Total (Opsional, tapi bagus disiapkan di sini)
        $grandTotal = 0;
        foreach ($carts as $cart) {
            foreach ($cart->items as $item) {
                // Ambil harga dari relasi produk
                $product = $item->id_product_diamart ? $item->productDiamart : $item->productDiraditya;
                $price = $product->price ?? 0;
                $grandTotal += $price * $item->qty;
            }
        }

        return view('cart.index', compact('carts', 'grandTotal'));
    }

    // --- 2. FUNGSI UTAMA: TAMBAH KE KERANJANG (HANDLES ALL) ---
    // Menggantikan fungsi addDiamart dan addRaditya yang terpisah
    public function addToCart(Request $request)
    {
        $user = Auth::user();

        // A. Validasi Input Dasar
        // Kita tidak tahu produk mana yang dikirim, jadi kita cek manual nanti
        $targetUnit = null;
        $productId = null;
        $maxStock = 0;

        if ($request->has('id_product_diamart')) {
            $targetUnit = 'diamart';
            $productId = $request->id_product_diamart;
            $product = ProductDiamart::find($productId);
        } elseif ($request->has('id_product_diraditya')) { // pastikan nama input sesuai form
            $targetUnit = 'raditya';
            $productId = $request->id_product_diraditya;
            $product = ProductRaditya::find($productId);
        } else {
            return back()->with('error', 'Produk tidak valid.');
        }

        // Cek apakah produk fisik ada di database?
        if (!$product) {
            return back()->with('error', 'Produk tidak ditemukan.');
        }

        // B. Cek Keranjang Existing User
        $existingCart = Cart::where('id_user', $user->id)->first();

        // C. LOGIKA VALIDASI UNIT BISNIS (SATPAM)
        if ($existingCart) {
            // Jika keranjang sudah ada, TAPI unit bisnisnya beda
            if ($existingCart->business_unit != $targetUnit) {
                $currentUnit = ucfirst($existingCart->business_unit);
                $newUnit = ucfirst($targetUnit);

                // STOP PROSES & KIRIM ERROR
                return redirect()->back()->withErrors([
                    'error' => "Gagal! Keranjang Anda sedang aktif untuk <b>{$currentUnit}</b>.<br>
                                Tidak bisa dicampur dengan produk <b>{$newUnit}</b>.<br>
                                Selesaikan transaksi atau hapus keranjang dulu."
                ]);
            }
            // Jika lolos (Unit sama), gunakan cart ID yang sudah ada
            $cartId = $existingCart->id;
        } else {
            // Jika Belum Punya Keranjang -> BUAT BARU
            $newCart = Cart::create([
                'id_user' => $user->id,
                'business_unit' => $targetUnit,
            ]);
            $cartId = $newCart->id;
        }

        // D. PROSES SIMPAN ITEM (Cart Item)

        // Cek apakah item produk yang sama sudah ada di cart ini?
        $existingItem = CartItem::where('id_cart', $cartId)
            ->when($targetUnit == 'diamart', function ($q) use ($productId) {
                return $q->where('id_product_diamart', $productId);
            })
            ->when($targetUnit == 'raditya', function ($q) use ($productId) {
                return $q->where('id_product_diraditya', $productId);
            })
            ->first();

        $qtyToAdd = $request->qty ?? 1;

        if ($existingItem) {
            // Cek Stok Sebelum Update
            if (($existingItem->qty + $qtyToAdd) > $product->stock) {
                return back()->with('error', "Stok tidak cukup. Sisa stok: {$product->stock}");
            }
            $existingItem->increment('qty', $qtyToAdd);
        } else {
            // Cek Stok Sebelum Create
            if ($qtyToAdd > $product->stock) {
                return back()->with('error', "Stok tidak cukup. Sisa stok: {$product->stock}");
            }

            CartItem::create([
                'id_cart' => $cartId,
                'id_product_diamart' => ($targetUnit == 'diamart') ? $productId : null,
                'id_product_diraditya' => ($targetUnit == 'raditya') ? $productId : null,
                'qty' => $qtyToAdd
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Produk berhasil masuk keranjang');
    }

    // --- 3. UPDATE QUANTITY (+/-) ---
    public function update(Request $request, $itemId)
    {
        $item = CartItem::findOrFail($itemId);

        // Ambil produk terkait untuk cek stok
        $product = $item->id_product_diamart ? $item->productDiamart : $item->productDiraditya;
        $maxStock = $product->stock;

        // Validasi input manual dan batas stok
        $request->validate([
            'qty' => "required|integer|min:1|max:$maxStock"
        ], [
            'qty.max' => "Maaf, stok hanya tersedia $maxStock unit."
        ]);

        $item->update([
            'qty' => $request->qty
        ]);

        return redirect()->back()->with('success', 'Keranjang diperbarui.');
    }

    // --- 4. HAPUS ITEM ---
    public function remove($itemId)
    {
        $item = CartItem::findOrFail($itemId);

        // Ambil cart ID sebelum dihapus untuk pengecekan nanti
        $cartId = $item->id_cart;

        $item->delete();

        // Cek: Apakah keranjang jadi kosong melompong setelah item ini dihapus?
        $remainingItems = CartItem::where('id_cart', $cartId)->count();

        if ($remainingItems == 0) {
            // Jika kosong, hapus Keranjang Utama (Header Cart) juga
            // Agar user bisa belanja Unit lain tanpa terhalang "Kunci Unit"
            Cart::find($cartId)->delete();
        }

        return redirect()->back()->with('success', 'Produk dihapus dari keranjang.');
    }

    // --- 5. CHECKOUT (Menuju Halaman Payment) ---
    public function checkout()
    {
        return redirect()->route('payment.index');
    }
}
