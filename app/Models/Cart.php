<?php

namespace App\Models;

use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{

    use HasFactory;
    protected $fillable = [
        'customer_id', 'guest_token', 'product_id', 'price', 'quantity', 'sub_total', 'token'
    ];

    public function products()
    {
        return $this->hasOne( Product::class, 'id', 'product_id' );
    }

    public function rocketResponse() {
        return $this->hasOne(CartShiprocketResponse::class, 'cart_token', 'guest_token');
    }

    
}
