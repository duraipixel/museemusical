<?php

namespace App\Models\Product;

use App\Models\Category\MainCategory;
use App\Models\Master\Brands;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'product_name',
        'hsn_code',
        'product_url',
        'sku',
        'price',
        'sale_price',
        'sale_start_date',
        'sale_end_date',
        'status',
        'quantity',
        'has_video_shopping',
        'stock_status',
        'brand_id',
        'category_id',
        'tag_id',
        'label_id',
        'is_display_home',
        'is_featured',
        'is_best_selling',
        'is_new',
        'tax_id',
        'description',
        'technical_information',
        'feature_information',
        'specification',
        'brochure_upload',
        'base_image',
        'approved_by',
        'approved_at',
        'added_by'
    ];

    public function productCategory()
    {
        return $this->hasOne(ProductCategory::class, 'id', 'category_id');
    }

    public function productBrand()
    {
        return $this->hasOne(Brands::class, 'id', 'brand_id');
    }

    public function productTag()
    {
        return $this->hasOne(MainCategory::class, 'id', 'tag_id')->where(['slug' => 'product-tags', 'status' => 'published']);
    }

    public function productLabel()
    {
        return $this->hasOne(MainCategory::class, 'id', 'label_id')->where(['slug' => 'product-labels', 'status' => 'published']);
    }

    public function productImages()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'id');
    }

    public function productMeasurement()
    {
        return $this->hasOne(ProductMeasurement::class, 'product_id', 'id');
    }

    public function productDiscount()
    {
        return $this->hasOne(ProductDiscount::class, 'product_id', 'id');
    }

    public function productAttributes()
    {
        return $this->hasMany(ProductWithAttributeSet::class, 'product_id', 'id');
    }

    public function productMeta()
    {
        return $this->hasOne(ProductMetaTag::class, 'product_id', 'id');
    }

    public function userInfo()
    {
        return $this->hasOne(User::class, 'id', 'added_by');
    }

    public function productRelated()
    {
        return $this->hasMany(ProductRelatedRelation::class, 'from_product_id', 'id');
    }

    public function productCrossSale()
    {
        return $this->hasMany(ProductCrossSaleRelation::class, 'from_product_id', 'id');
    }

}
