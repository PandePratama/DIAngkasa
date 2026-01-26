<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $fillable = [
        'user_id',
        'invoice_code',
        'grand_total',
        'payment_type',
        'tenure',
        'status',

    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'id_order');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
