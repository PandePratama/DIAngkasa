<?php

namespace App\Listeners;

use App\Models\Cart;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LoadUserCart
{
    public function handle(Login $event)
    {
        $cart = Cart::with('items.product.primaryImage')
            ->where('user_id', $event->user->id)
            ->first();

        if (!$cart) return;

        $sessionCart = [];

        foreach ($cart->items as $item) {
            $sessionCart[$item->product_id] = [
                'id'            => $item->product_id,
                'name'          => $item->product->name,
                'price'         => $item->price,
                'qty'           => $item->qty,
                'purchase_type' => $item->purchase_type,
                'tenor'         => $item->tenor,
                'image'         => optional($item->product->primaryImage)->image_path,
            ];
        }

        session(['cart' => $sessionCart]);
    }
}
