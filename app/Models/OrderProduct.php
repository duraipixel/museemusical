<?php

namespace App\Models;

use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'hsn_code' ,
        'sku',
        'quantity',
        'price',
        'tax_amount',
        'tax_percentage',
        'sub_total'
    ];

    public function products()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}
