<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi balik ke Transaksi
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
