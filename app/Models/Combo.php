<?php

namespace App\Models;

use App\Models\Product\ComboProduct;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Combo extends Model
{
    use HasFactory;
    protected $fillable = [
        'combo_name',
        'slug',
        'tag_line',
        'order_by',
        'status',
        'show_home_page',
    ];
    public function collectionProducts()
    {
        return $this->hasMany(ComboProduct::class, 'combo_product_id', 'id' ); 

    }
}
