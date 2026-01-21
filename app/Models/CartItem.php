<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function cart()
    {
        return $this->belongsTo(Cart::class, 'id_cart');
    }

    // Relasi ke Product (Salah satu akan null)
    public function productDiamart()
    {
        return $this->belongsTo(ProductDiamart::class, 'id_product_diamart');
    }

    public function productDiraditya()
    {
        return $this->belongsTo(ProductRaditya::class, 'id_product_diraditya');
    }

    // Accessor Helper untuk mendapatkan objek produk yang "nyata" tanpa peduli tipe
    public function getProductAttribute()
    {
        return $this->productDiamart ?? $this->productDiraditya;
    }
}
