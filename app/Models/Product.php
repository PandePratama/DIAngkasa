<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'product_code',
        'name',
        'specification',
        'category_id',
        'brand_id',
        'stock',
        'price',
        'price_3_months',
        'price_6_months',
        'price_9_months',
        'price_12_months',
    ];

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)
            ->where('is_primary', true);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brands::class);
    }

    // Relasi: 1 Produk bisa punya banyak transaksi kredit
    public function creditTransactions()
    {
        return $this->hasMany(CreditTransaction::class);
    }
}
