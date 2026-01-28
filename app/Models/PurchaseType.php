<?php

namespace App\Models;

use App\Enums\StatusPurchaseTypes;
use Illuminate\Database\Eloquent\Model;

class PurchaseType extends Model
{
    protected $guarded = ['id'];
    protected $fillable = [
        'id',
        'code',
        'name'
    ];

    protected $casts = [
        'payment_method' => StatusPurchaseTypes::class,
    ];
}
