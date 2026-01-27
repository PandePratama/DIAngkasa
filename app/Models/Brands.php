<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Fluent\Concerns\Has;

class Brands extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function productsDiraditya()
    {
        return $this->hasMany(ProductRaditya::class, 'id_brand');
    }
}
