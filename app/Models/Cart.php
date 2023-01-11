<?php

namespace App\Models;

use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{

    use HasFactory;
    protected $fillable = [
        'customer_id', 'guest_token', 'product_id', 'price', 'quantity', 'sub_total'
    ];

    public function products()
    {
        return $this->hasMany( Product::class, 'id', 'product_id' );
    }

    
}
