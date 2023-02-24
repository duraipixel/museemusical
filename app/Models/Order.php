<?php

namespace App\Models;

use App\Models\Master\Customer;
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
        'billing_name',
        'billing_email',
        'billing_mobile_no',
        'billing_address_line1',
        'billing_address_line2',
        'billing_landmark',
        'billing_country',
        'billing_post_code',
        'billing_state',
        'billing_city',
        'shipping_name',
        'shipping_email',
        'shipping_mobile_no',
        'shipping_address_line1',
        'shipping_address_line2',
        'shipping_landmark',
        'shipping_country',
        'shipping_post_code',
        'shipping_state',
        'shipping_city',
        'description',
        'order_status_id',
        'status',
        'rocket_charge_name',
        'rocket_charge_response',
        'payment_id',
        'payment_response_id'        
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderProduct::class, 'order_id', 'id');
    }

    public function payments()
    {
        return $this->hasOne(Payment::class,'order_id', 'id');
    }

    public function customer()
    {
        return $this->hasOne(Customer::class,'id', 'customer_id');
    }

    public function tracking()
    {
        return $this->hasMany(OrderHistory::class, 'order_id', 'id');
    }

}
