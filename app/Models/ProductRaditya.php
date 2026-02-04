<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRaditya extends Model
{
    use HasFactory;

    protected $table = 'product_diraditya';

    protected $fillable = [
        'sku',
        'id_category',
        'id_brand',
        'name',
        'hpp',
        'price',
        'stock',
        'desc',
        'warranty',
    ];

    // ================= RELATIONS =================

    public function category()
    {
        return $this->belongsTo(Category::class, 'id_category');
    }

    public function brand()
    {
        return $this->belongsTo(Brands::class, 'id_brand');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'id_product_diraditya');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class, 'id_product_diraditya')
            ->latest();
    }
}
