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

        // Load keranjang user beserta item dan gambar produknya
        $carts = Cart::with([
            'items.productDiamart.primaryImage',
            'items.productDiraditya.primaryImage'
        ])
            ->where('id_user', $user->id)
            ->get();

        // Hitung Grand Total
        $grandTotal = 0;
        foreach ($carts as $cart) {
            foreach ($cart->items as $item) {
                $product = $item->id_product_diamart ? $item->productDiamart : $item->productDiraditya;
                // Pastikan produk masih ada (tidak null) untuk menghindari error
                if ($product) {
                    $grandTotal += $product->price * $item->qty;
                }
            }
        }

        return view('cart.index', compact('carts', 'grandTotal'));
    }

    // --- 2. FUNGSI UTAMA: TAMBAH KE KERANJANG ---
    public function addToCart(Request $request)
    {
        $user = Auth::user();

        // 1. Tentukan Target Unit & Validasi Produk
        $targetUnit = null;
        $productId = null;
        $product = null;

        if ($request->has('id_product_diamart')) {
            $targetUnit = 'diamart';
            $productId = $request->id_product_diamart;
            $product = ProductDiamart::find($productId);
        } elseif ($request->has('id_product_diraditya')) {
            $targetUnit = 'raditya';
            $productId = $request->id_product_diraditya;
            $product = ProductRaditya::find($productId);
        } else {
            return back()->with('error', 'Data produk tidak valid.');
        }

        if (!$product) {
            return back()->with('error', 'Produk tidak ditemukan di database.');
        }

        // 2. Cek Keranjang User yang Sudah Ada
        // Kita cari keranjang apapun milik user ini
        $existingCart = Cart::where('id_user', $user->id)->first();

        // 3. Validasi Cross-Business Unit
        if ($existingCart) {
            // Jika user sudah punya keranjang, CEK UNITNYA
            if ($existingCart->business_unit != $targetUnit) {
                // Konversi nama unit biar enak dibaca user
                $currentUnitName = ($existingCart->business_unit == 'diamart') ? 'Minimarket (Diamart)' : 'Gadget (Raditya)';
                $targetUnitName = ($targetUnit == 'diamart') ? 'Minimarket (Diamart)' : 'Gadget (Raditya)';

                return redirect()->back()->withErrors([
                    'error' => "Gagal! Keranjang Anda saat ini berisi produk <b>{$currentUnitName}</b>.<br>
                                Tidak bisa dicampur dengan produk <b>{$targetUnitName}</b>.<br>
                                Silakan <b>selesaikan pembayaran</b> atau <b>kosongkan keranjang</b> terlebih dahulu."
                ]);
            }

            // Jika unit sama, gunakan ID keranjang lama
            $cartId = $existingCart->id;
        } else {
            // Jika belum punya keranjang sama sekali, BUAT BARU
            // (Disinilah seharusnya create dilakukan, bukan di awal fungsi)
            $newCart = Cart::create([
                'id_user' => $user->id,
                'business_unit' => $targetUnit
            ]);
            $cartId = $newCart->id;
        }

        // 4. Proses Insert/Update Item (CartItem)
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
            // -- Update Qty Item Lama --
            // Cek stok total nanti (qty lama + qty baru)
            if (($existingItem->qty + $qtyToAdd) > $product->stock) {
                return back()->with('error', "Stok tidak mencukupi. Maksimal tambahan: " . ($product->stock - $existingItem->qty));
            }
            $existingItem->increment('qty', $qtyToAdd);
        } else {
            // -- Buat Item Baru --
            if ($qtyToAdd > $product->stock) {
                return back()->with('error', "Stok barang tersisa {$product->stock}.");
            }

            CartItem::create([
                'id_cart' => $cartId,
                'id_product_diamart' => ($targetUnit == 'diamart') ? $productId : null,
                'id_product_diraditya' => ($targetUnit == 'raditya') ? $productId : null,
                'qty' => $qtyToAdd
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    // --- 3. UPDATE QUANTITY ---
    public function update(Request $request, $itemId)
    {
        $item = CartItem::findOrFail($itemId);

        // Identifikasi produk
        $product = $item->id_product_diamart ? $item->productDiamart : $item->productDiraditya;

        if (!$product) {
            $item->delete(); // Hapus item hantu jika produk master hilang
            return back()->with('error', 'Produk tidak ditemukan.');
        }

        $maxStock = $product->stock;

        $request->validate([
            'qty' => "required|integer|min:1|max:$maxStock"
        ], [
            'qty.max' => "Stok hanya tersedia $maxStock unit."
        ]);

        $item->update(['qty' => $request->qty]);

        return redirect()->back()->with('success', 'Keranjang diperbarui.');
    }

    // --- 4. HAPUS ITEM ---
    public function remove($itemId)
    {
        $item = CartItem::findOrFail($itemId);
        $cartId = $item->id_cart;

        // Hapus Item
        $item->delete();

        // Cek sisa item di keranjang ini
        $count = CartItem::where('id_cart', $cartId)->count();

        if ($count == 0) {
            // Jika kosong, HAPUS HEADER CART agar status unit 'terkunci' hilang
            Cart::where('id', $cartId)->delete();
        }

        return redirect()->back()->with('success', 'Item dihapus.');
    }

    // --- 5. CHECKOUT ---
    public function checkout()
    {
        // Pastikan keranjang tidak kosong sebelum ke payment
        $user = Auth::user();
        $hasCart = Cart::where('id_user', $user->id)->exists();

        if (!$hasCart) {
            return redirect()->route('home')->with('error', 'Keranjang Anda kosong.');
        }

        return redirect()->route('payment.index');
    }
}
