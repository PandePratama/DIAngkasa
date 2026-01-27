<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDiamart extends Model
{
    use HasFactory;

    protected $table = 'product_diamart'; // Wajib didefinisikan
    protected $guarded = ['id'];

    // protected $fillable = [

    // ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'id_category');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'id_product_diamart');
    }

    // Helper untuk ambil gambar pertama
    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class, 'id_product_diamart')->latest();
    }
}
