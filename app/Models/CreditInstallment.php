<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditInstallment extends Model
{
    protected $table = 'credit_installment';
    protected $guarded = ['id'];
    protected $dates = ['due_date', 'paid_at'];

    protected $fillable = [
        'id_credit_transaction',
        'id_user',
        'installment_month',
        'amount',
        'admin_fee',
        'balance_before',
        'balance_after'
    ];

    public function creditTransaction() // atau function parent()
    {
        // Parameter kedua wajib 'id_credit_transaction'
        return $this->belongsTo(CreditTransaction::class, 'id_credit_transaction');
    }

    public function transaction()
    {
        return $this->belongsTo(CreditTransaction::class);
    }
}
