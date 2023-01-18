<?php

namespace App\Models;

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
}
