<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $fillable = [
        'id_user',
        'purchase_type_id',
        'invoice_code',
        'grand_total',
        'balance_after',
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
        return $this->belongsTo(User::class, 'id_user');
    }
    public function purchaseType()
    {
        return $this->belongsTo(PurchaseType::class, 'purchase_type_id');
    }

    public function getPaymentLabelAttribute()
    {
        if ($this->payment_method == 'balance') return 'Potong Saldo';
        if ($this->payment_method == 'cash') return 'Cash / Tunai';
        return '-';
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class, 'transaction_id');
    }
}
