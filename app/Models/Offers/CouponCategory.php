<?php

namespace App\Models\Offers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CouponCategory extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'coupon_id',
        'customer_id',
        'quantity',
    ];
}
