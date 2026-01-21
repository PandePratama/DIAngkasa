<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function order()
    {
        return $this->belongsTo(Order::class, 'id_order');
    }

    // Relasi ke Product (Untuk keperluan tracking link, meski snapshot data sudah disimpan)
    public function productDiamart()
    {
        return $this->belongsTo(ProductDiamart::class, 'id_product_diamart');
    }

    public function productDiraditya()
    {
        return $this->belongsTo(ProductRaditya::class, 'id_product_diraditya');
    }
}
