<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $table = 'cart_items';

    // Sesuaikan dengan kolom yang ada di database Anda
    protected $fillable = [
        'id_cart',
        'id_product_diamart',
        'id_product_diraditya',
        'qty'
    ];

    // Relasi ke Cart Header
    public function cart()
    {
        return $this->belongsTo(Cart::class, 'id_cart');
    }

    // Relasi ke Produk Diamart
    public function productDiamart()
    {
        return $this->belongsTo(ProductDiamart::class, 'id_product_diamart');
    }

    // Relasi ke Produk Raditya
    public function productDiraditya()
    {
        return $this->belongsTo(ProductRaditya::class, 'id_product_diraditya');
    }
}
