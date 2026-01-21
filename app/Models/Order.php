<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'id_order');
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'id_order');
    }

    public function installments()
    {
        // Untuk cicilan Raditya
        return $this->hasMany(Installment::class, 'id_order');
    }

    public function purchaseType()
    {
        return $this->belongsTo(PurchaseType::class, 'id_purchase_type');
    }
}
