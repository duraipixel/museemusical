<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCollection extends Model
{
    use HasFactory;
    protected $fillable = [
        'collection_name',
        'slug',
        'tag_line',
        'order_by',
        'status',
        'show_home_page',
        'can_map_discount'
    ];

    public function collectionProducts()
    {
        return $this->hasMany(ProductCollectionProduct::class, 'product_collection_id', 'id' ); 
    }
    
}
