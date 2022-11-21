<?php

namespace App\Models\Offers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupons extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'coupon_name',
        'coupon_code',
        'coupon_sku',
        'start_date',
        'end_date',
        'quantity',
        'used_quantity',
        'calculate_type',
        'calculate_value',
        'minimum_order_value',
        'is_discount_on',
        'coupon_type',
        'is_applied_all',
        'repeated_use_count',
        'is_discount_on',
        'status',
        'order_by',
    ];

    public function couponProducts()
    {
        return $this->hasMany(CouponProduct::class, 'coupon_id', 'id');
    }

    public function couponCustomers()
    {
        return $this->hasMany(CouponCustomer::class, 'coupon_id', 'id');
    }

    public function couponCategory()
    {
        return $this->hasMany(CouponCategory::class, 'coupon_id', 'id');
    }


}
