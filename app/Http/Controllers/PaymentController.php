<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Providers\CreditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $cart = Cart::with('items.product')
            ->where('user_id', $user->id)
            ->first();

        $cartItems = $cart ? $cart->items : collect();

        // 🔥 TOTAL SELALU DARI PRODUCT PRICE
        $total = $cartItems->sum(fn ($item) => $item->qty * $item->product->price);

        return view('payment.index', compact('cartItems', 'total'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'payment_method'  => 'required|in:cash,credit',
            'shipping_method' => 'required'
        ]);

        $user = auth()->user();

        $cart = Cart::with('items.product')
            ->where('user_id', $user->id)
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return back()->with('error', 'Keranjang kosong');
        }

        // 🔥 HITUNG TOTAL ORDER
        $total = $cart->items->sum(fn ($item) => $item->qty * $item->product->price);

        // 🔐 VALIDASI CREDIT (SEBELUM TRANSACTION)
        if ($request->payment_method === 'credit') {

            $sisaLimit = CreditService::remaining($user);

            if ($sisaLimit < $total) {
                return back()->with('error', 'Limit kredit tidak mencukupi');
            }
        }

        // 🔥 TRANSACTION AMAN
        DB::transaction(function () use ($request, $user, $cart, $total) {

            // 🔻 POTONG CREDIT (JIKA CREDIT)
            if ($request->payment_method === 'credit') {
                $user->decrement('credit_limit', $total);
            }

            // 💾 SIMPAN ORDER
            $order = Order::create([
                'user_id'         => $user->id,
                'payment_method'  => $request->payment_method,
                'shipping_method' => $request->shipping_method
            ]);

            // 💾 SIMPAN ORDER ITEMS
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item->product_id,
                    'qty'        => $item->qty,
                ]);
            }

            // 🧹 KOSONGKAN CART
            $cart->items()->delete();
        });

        return redirect()
            ->route('payment.index')
            ->with('success', 'Pesanan berhasil dibuat');
    }
}
