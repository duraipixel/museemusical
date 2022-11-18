<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttributeSet extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'slug',
        'tag_line',
        'is_searchable',
        'is_comparable',
        'is_use_in_product_listing',
        'status',
        'order_by',
    ];
}
