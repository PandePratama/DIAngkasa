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
        'id_unit_kerja',
        'password',
        'role',
        'saldo'
        'no_telp',
        'nik',

    ];

    protected $guarded = ['id'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relasi
    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'id_unit_kerja');
    }

    public function activeCart()
    {
        // Asumsi 1 User hanya punya 1 Cart aktif
        return $this->hasOne(Cart::class, 'id_user');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'id_user');
    }

    public function transactions()
    {
<<<<<<< HEAD
        return $this->hasMany(Transaction::class, 'id_user');
=======
        return $this->hasMany(Transaction::class, 'user_id');
    public function cart()
    {
        return $this->hasOne(Cart::class, 'id_user');
>>>>>>> 46a546bea3f9010ad6a230d1537a3986e526ce73
    }
}
