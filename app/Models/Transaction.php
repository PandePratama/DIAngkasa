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
        'purchase_type_id',
        'tenure',
        'status',
        'balance_after'

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
    public function purchaseType()
    {
        // Pastikan namespace PurchaseType benar
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
