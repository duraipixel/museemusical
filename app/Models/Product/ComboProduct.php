<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComboProduct extends Model
{
    use HasFactory;
    protected $fillable = [
        'combo_product_id',
        'product_id',
        'order_by',
    ];
    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id' ); 
    }
}
