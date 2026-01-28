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
        // 1. Eager Load relasi baru (Diamart & Diraditya)
        // Hapus 'items.product.primaryImage' karena relasi 'product' sudah tidak ada
        $cart = Cart::with([
            'items.productDiamart.primaryImage',
            'items.productDiraditya.primaryImage'
        ])
            ->where('id_user', $event->user->id)
            ->first();

        if (!$cart) return;

        $sessionCart = [];

        foreach ($cart->items as $item) {
            // 2. Deteksi produk aktif (Sembako atau Gadget)
            $product = $item->id_product_diamart
                ? $item->productDiamart
                : $item->productDiraditya;

            // Skip jika data produk master tidak ditemukan (misal terhapus)
            if (!$product) continue;

            // 3. Susun data session
            // Perhatikan: price diambil dari $product, bukan $item
            // Tenor & purchase_type dihapus karena sekarang dipilih saat checkout
            $sessionCart[$product->id] = [
                'id'            => $product->id,
                'name'          => $product->name,
                'price'         => $product->price, // Ambil dari Master Produk
                'qty'           => $item->qty,
                'image'         => optional($product->primaryImage)->image_path,
                'business_unit' => $item->id_product_diamart ? 'diamart' : 'raditya' // Penanda unit
            ];
        }

        // Simpan ke session 'cart'
        session(['cart' => $sessionCart]);

        // Opsional: Simpan jumlah jenis barang untuk badge notifikasi
        session(['cart_count' => count($sessionCart)]);
    }
}
