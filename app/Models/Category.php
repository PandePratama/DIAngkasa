<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $fillable = [
        'group', // <--- Tambahkan ini
        'category_name',
        'slug'
    ];

    // Sama seperti brand, relasi ke kedua jenis produk
    public function productsDiamart()
    {
        return $this->hasMany(ProductDiamart::class, 'id_category');
    }

    public function productsDiraditya()
    {
        return $this->hasMany(ProductRaditya::class, 'id_category');
    }
}
