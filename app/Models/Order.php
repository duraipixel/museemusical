<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'order_no',
        'shipping_options',
        'shipping_type',
        'amount',
        'tax_id',
        'tax_percentage',
        'tax_amount',
        'shipping_amount',
        'discount_amount',
        'coupon_amount',
        'coupon_code',
        'sub_total',
        'description',
        'order_status_id',
        'status',
        'payment_id',
        'payment_response_id'        
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderProduct::class, 'order_id', 'id');
    }

}
