<?php

namespace App\Models\Offers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CouponProduct extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'coupon_id',
        'product_id',
        'quantity',
    ];
}
