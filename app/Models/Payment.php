<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_no',
        'amount',
        'paid_amount',
        'payment_type',
        'payment_mode',
        'response',
        'description',
        'status'
    ];

    public function orders()
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }
}
