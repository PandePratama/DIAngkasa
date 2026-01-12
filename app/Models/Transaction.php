<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'nip',
        'admin_name',
        'amount',
        'saldo_awal',
        'saldo_akhir'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
