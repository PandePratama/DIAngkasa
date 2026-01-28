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

    // Tambahkan di dalam class CartController

    public function index()
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        // 1. Ambil Keranjang User
        // Kita gunakan 'with' (Eager Loading) agar query lebih hemat
        $carts = \App\Models\Cart::with([
            'items.productDiamart',   // Load data produk sembako
            'items.productDiraditya'  // Load data produk elektronik
        ])
            ->where('user_id', $user->id)
            ->get();

        // 2. Hitung Grand Total (Opsional, untuk display ringkasan)
        $grandTotal = 0;
        foreach ($carts as $cart) {
            foreach ($cart->items as $item) {
                // Ambil harga dari relasi produk (karena price tidak disimpan di cart_items)
                $price = 0;
                if ($item->id_product_diamart && $item->productDiamart) {
                    $price = $item->productDiamart->price;
                } elseif ($item->id_product_diraditya && $item->productDiraditya) {
                    $price = $item->productDiraditya->price;
                }

                $grandTotal += $price * $item->qty;
            }
        }

        return view('cart.index', compact('carts', 'grandTotal'));
    }
    // --- 1. TAMBAH KERANJANG DIAMART (SEMBAKO) ---
    // --- 1. TAMBAH KERANJANG DIAMART (SEMBAKO) ---
    public function addDiamart(Request $request, $id)
    {
        $user = Auth::user();

        $cart = Cart::firstOrCreate([
            'user_id' => $user->id,
            'business_unit' => 'diamart'
        ]);

        $item = CartItem::where('id_cart', $cart->id)
            ->where('id_product_diamart', $id)
            ->first();

        if ($item) {
            // PERBAIKAN: Jika sudah ada, jangan di-increment.
            // Langsung saja arahkan ke cart agar user mengaturnya di sana.
            return redirect()->route('cart.index')->with('info', 'Produk sudah ada di keranjang.');
        } else {
            CartItem::create([
                'id_cart' => $cart->id,
                'id_product_diamart' => $id,
                'qty' => 1 // Set awal selalu 1
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    // --- 2. TAMBAH KERANJANG RADITYA (ELEKTRONIK) ---
    public function addRaditya(Request $request, $id)
    {
        $user = Auth::user();
        $product = ProductRaditya::findOrFail($id);

        $cart = Cart::firstOrCreate(
            ['user_id' => $user->id, 'business_unit' => 'raditya']
        );

        $item = CartItem::where('id_cart', $cart->id)
            ->where('id_product_diraditya', $product->id)
            ->first();

        if ($item) {
            // PERBAIKAN: Sama seperti Diamart, jangan gunakan increment()
            return redirect()->route('cart.index')->with('info', 'Produk sudah ada di keranjang.');
        } else {
            CartItem::create([
                'id_cart'              => $cart->id,
                'id_product_diamart'   => null,
                'id_product_diraditya' => $product->id,
                'qty'                  => 1,
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Gadget berhasil masuk keranjang!');
    }

    // --- 3. UPDATE QUANTITY (+/-) ---
    // --- 3. UPDATE QUANTITY DENGAN VALIDASI STOK ---
    public function update(Request $request, $itemId)
    {
        $item = CartItem::findOrFail($itemId);

        // Ambil produk terkait untuk cek stok
        $product = $item->id_product_diamart
            ? $item->productDiamart
            : $item->productDiraditya;

        $maxStock = $product->stock; // Ambil stok asli dari gudang

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
        $item->delete();

        return redirect()->back()->with('success', 'Produk dihapus dari keranjang.');
    }
}
