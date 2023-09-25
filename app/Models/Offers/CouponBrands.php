<?php

namespace App\Models\Offers;

use App\Models\Master\Brands;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponBrands extends Model
{
    use HasFactory;

    protected $fillable = [
        'coupon_id',
        'brand_id',
        'quantity',
    ];

    public function brands()
    {
        return $this->hasOne(Brands::class, 'id', 'brand_id');
    }
}
