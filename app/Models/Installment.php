<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    // protected $casts = [
    //     'due_date' => 'date',
    // ];

    protected $fillable = [
        'id',
        'id_order',
        'installment_number',
        'amount',
        'due_date',
        'status',
        'notes'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'id_order');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'id_order');
    }
}
