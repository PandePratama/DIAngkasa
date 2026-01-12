<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // ================= CART =================

    public function index()
    {
        $cart = Cart::with('items.product')
            ->where('user_id', Auth::id())
            ->first();

        $items = $cart?->items ?? collect();

        $total = $items->sum(fn($item) => $item->price * $item->qty);

        return view('cart.index', compact('items', 'total'));
    }

    public function add(Request $request, Product $product)
    {
        $request->validate([
            'purchase_type' => 'required|in:cash,credit',
            'tenor' => 'nullable|in:3,6,9,12'
        ]);

        $cart = Cart::firstOrCreate([
            'user_id' => Auth::id()
        ]);

        $type  = $request->purchase_type;
        $tenor = $type === 'credit' ? (int) $request->tenor : null;

        // FIX match error
        $price = $type === 'credit'
            ? match ((int) $tenor) {
                3  => $product->price_3_months, 
                6  => $product->price_6_months,
                9  => $product->price_9_months,
                12 => $product->price_12_months,
                default => abort(400, 'Tenor tidak valid')
            }
            : $product->price;

        $item = $cart->items()
            ->where('product_id', $product->id)
            ->where('purchase_type', $type)
            ->where('tenor', $tenor)
            ->first();

        if ($item) {
            $item->increment('qty');
        } else {
            $cart->items()->create([
                'product_id'    => $product->id,
                'purchase_type' => $type,
                'tenor'         => $tenor,
                'price'         => $price,
                'qty'           => 1,
            ]);
        }

        return redirect()->route('cart.index')
            ->with('success', 'Produk ditambahkan ke keranjang');
    }

    public function update(Request $request, $itemId)
    {
        $request->validate(['qty' => 'required|min:1']);

        Auth::user()->cart
            ->items()
            ->where('id', $itemId)
            ->update(['qty' => $request->qty]);

        return back();
    }

    public function remove($itemId)
    {
        Auth::user()->cart
            ->items()
            ->where('id', $itemId)
            ->delete();

        return back();
    }

    // ================= PAYMENT =================

    public function payment()
    {
        $cart = Cart::with('items.product.primaryImage')
            ->where('user_id', Auth::id())
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang masih kosong');
        }

        $total = $cart->items->sum(fn($item) => $item->price * $item->qty);

        return view('payment.index', [
            'cartItems' => $cart->items,
            'total' => $total,
        ]);
    }
}
