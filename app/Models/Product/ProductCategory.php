<?php

namespace App\Models\Product;

use App\Models\CategoryMetaTags;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategory extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'name',
        'parent_id',
        'description',
        'image',
        'is_featured',
        'status',
        'order_by',
        'added_by',
        'tag_line',
        'tax_id',
        'is_home_menu',
        'updated_by'
    ];

    public function meta()
    {
        return $this->belongsTo(CategoryMetaTags::class, 'category_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id', 'id');
    }
}
