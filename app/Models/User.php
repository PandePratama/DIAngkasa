<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'nip',
        'nik',
        'no_telp',
        'id_unit_kerja',
        'password',
        'role',
        'saldo',
        'no_telp',
        'address',

    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // ================= RELASI =================

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'id_unit_kerja');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'id_user');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'id_user');
    }
    public function cart()
    {
        return $this->hasMany(Transaction::class, 'id_user');
    }

    // [TAMBAHKAN INI] Relasi ke Transaksi Kredit
    public function creditTransactions()
    {
        return $this->hasMany(CreditTransaction::class, 'id_user');
    }
}
