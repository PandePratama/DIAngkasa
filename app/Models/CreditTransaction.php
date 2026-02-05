<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditTransaction extends Model
{
    protected $table = 'credit_transactions';
    protected $guarded = ['id'];
    protected $fillable = [
        'id_user',
        'id_product',
        'tenor',
        'up_price',
        'monthly_amount',
        'admin_fee',
        'total_paid_month',
        'dp_amount',
        'status'
    ];
    protected $casts = [
        'status'
    ];

    // Relasi ke Produk
    public function user()
    {
        // Pastikan foreign key di parameter kedua benar
        return $this->belongsTo(User::class, 'id_user');
    }

    public function product()
    {
        return $this->belongsTo(ProductRaditya::class, 'id_product');
    }

    // Relasi ke Rincian Cicilan (Kartu Piutang)
    public function installments()
    {
        // Tambahkan parameter kedua: 'id_credit_transaction' (nama kolom asli di DB)
        return $this->hasMany(CreditInstallment::class, 'id_credit_transaction');
    }
}
