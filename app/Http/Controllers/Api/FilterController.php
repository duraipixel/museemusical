<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category\SubCategory;
use App\Models\Product\Product;
use App\Models\Product\ProductCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FilterController extends Controller
{
    public function getFilterStaticSideMenu()
    {

        $product_availability = array(
            'in_stock' => 'In Stock',
            'coming_soon' => 'Upcoming',
            'out_of_stock' => 'Out Of Stock'

        );

        $video_shopping         = array('video_shopping' => 'Video Shopping is available');

        $sory_by                = array(
            array('id' => null, 'name' => 'Featured', 'slug' => 'is_featured'),
            array('id' => null, 'name' => 'Price: High to Low', 'slug' => 'price_high_to_low'),
            array('id' => null, 'name' => 'Price: Low to High', 'slug' => 'price_low_to_high'),
        );

        $tags                   = SubCategory::select('sub_categories.id', 'sub_categories.name', 'sub_categories.slug')
            ->join('main_categories', 'main_categories.id', '=', 'sub_categories.parent_id')
            ->where('sub_categories.status', 'published')
            ->where('main_categories.slug', 'product-tags')
            ->orderBy('sub_categories.order_by', 'asc')
            ->get()->toArray();


        $labels                   = SubCategory::select('sub_categories.id', 'sub_categories.name', 'sub_categories.slug')
            ->join('main_categories', 'main_categories.id', '=', 'sub_categories.parent_id')
            ->where('sub_categories.status', 'published')
            ->where('main_categories.slug', 'product-labels')
            ->orderBy('sub_categories.order_by', 'asc')
            ->get()->toArray();
        // dd( $tags );                       
        $sory_by                = array_merge($tags, $labels, $sory_by);

        $discounts              = ProductCollection::select('id', 'collection_name', 'slug')
            ->where('can_map_discount', 'yes')
            ->where('status', 'published')
            ->orderBy('order_by', 'asc')
            ->get()->toArray();

        $collection              = ProductCollection::select('id', 'collection_name', 'slug')
            ->where('can_map_discount', 'no')
            ->where('show_home_page', 'yes')
            ->where('status', 'published')
            ->orderBy('order_by', 'asc')
            ->get()->toArray();

        $response               = array(
            'product_availability' => $product_availability,
            'video_shopping' => $video_shopping,
            'sory_by' => $sory_by,
            'discounts' => $discounts,
            'collection' => $collection
        );

        return $response;
    }

    public function getProducts(Request $request)
    {

        $page                   = $request->page ?? 0;
        $filter_category        = $request->category;
        $filter_sub_category    = $request->scategory;
        $filter_availability    = $request->availability;
        $filter_brand           = $request->brand;
        $filter_discount        = $request->discount;
        $sort                   = $request->sort;

        $filter_availability_array = [];
        $filter_brand_array = [];
        $filter_discount_array = [];
        $filter_booking     = $request->booking;
        if (isset($filter_availability) && !empty($filter_availability)) {
            $filter_availability_array = explode("-", $filter_availability);
        }
        if (isset($filter_brand) && !empty($filter_brand)) {
            $filter_brand_array     = explode("_", $filter_brand);
        }

        if (isset($filter_discount) && !empty($filter_discount)) {
            $filter_discount_array     = explode("_", $filter_discount);
        }

        $limit = 6;
        $skip = (isset($page) && !empty($page)) ? ($page * $limit) : 0;

        $from   = 1 + ($page * $limit);
        $to     = $skip + $limit;

        $take_limit = $limit + ($page * $limit);
        $total  = Product::where('products.status', 'published')
            ->join('product_categories', 'product_categories.id', '=', 'products.category_id')
            ->leftJoin('product_categories as parent', 'parent.id', '=', 'product_categories.parent_id')
            ->join('brands', 'brands.id', '=', 'products.brand_id')
            ->when($filter_category != '', function ($q) use ($filter_category) {

                $q->where(function ($query) use ($filter_category) {
                    return $query->where('product_categories.slug', $filter_category)->orWhere('parent.slug', $filter_category);
                });
            })
            ->when($filter_sub_category != '', function ($q) use ($filter_sub_category) {
                return $q->where('product_categories.slug', $filter_sub_category);
            })
            ->when($filter_availability != '', function ($q) use ($filter_availability_array) {
                return $q->whereIn('products.stock_status', $filter_availability_array);
            })
            ->when($filter_brand != '', function ($q) use ($filter_brand_array) {
                return $q->whereIn('brands.slug', $filter_brand_array);
            })
            ->when($filter_booking == 'video_shopping', function ($q) {
                return $q->where('products.has_video_shopping', 'yes');
            })
            ->when($filter_discount != '', function ($q) use ($filter_discount_array) {
                $q->join('product_collections_products', 'product_collections_products.product_id', '=', 'products.id');
                $q->join('product_collections', 'product_collections.id', '=', 'product_collections_products.product_collection_id' );
                return $q->whereIn('product_collections.slug', $filter_discount_array);
            })
            ->count();

        $details = Product::select('products.*')->where('products.status', 'published')
            ->join('product_categories', 'product_categories.id', '=', 'products.category_id')
            ->leftJoin('product_categories as parent', 'parent.id', '=', 'product_categories.parent_id')
            ->join('brands', 'brands.id', '=', 'products.brand_id')
            ->when($filter_category != '', function ($q) use ($filter_category) {
                $q->where(function ($query) use ($filter_category) {
                    return $query->where('product_categories.slug', $filter_category)->orWhere('parent.slug', $filter_category);
                });
            })
            ->when($filter_sub_category != '', function ($q) use ($filter_sub_category) {
                return $q->where('product_categories.slug', $filter_sub_category);
            })
            ->when($filter_availability != '', function ($q) use ($filter_availability_array) {
                return $q->whereIn('products.stock_status', $filter_availability_array);
            })
            ->when($filter_brand != '', function ($q) use ($filter_brand_array) {
                return $q->whereIn('brands.slug', $filter_brand_array);
            })
            ->when($filter_booking == 'video_shopping', function ($q) {
                return $q->where('products.has_video_shopping', 'yes');
            })
            ->when($filter_discount != '', function ($q) use ($filter_discount_array) {
                $q->join('product_collections_products', 'product_collections_products.product_id', '=', 'products.id');
                $q->join('product_collections', 'product_collections.id', '=', 'product_collections_products.product_collection_id' );
                return $q->whereIn('product_collections.slug', $filter_discount_array);
            })
            ->when($sort == 'price_high_to_low', function($q){
                $q->orderBy('products.price', 'desc');
            })
            ->when($sort == 'price_low_to_high', function($q){
                $q->orderBy('products.price', 'asc');
            })
            ->when($sort == 'is_featured', function($q){
                $q->orderBy('products.is_featured', 'desc');
            })
            ->skip(0)->take($take_limit)
            ->get();

        $tmp = [];
        if (isset($details) && !empty($details)) {
            foreach ($details as $items) {

                $category               = $items->productCategory;
                $salePrices             = getProductPrice($items);
                
                $pro                    = [];
                $pro['id']              = $items->id;
                $pro['product_name']    = $items->product_name;
                $pro['category_name']   = $category->name ?? '';
                $pro['brand_name']      = $items->productBrand->brand_name ?? '';
                $pro['hsn_code']        = $items->hsn_code;
                $pro['product_url']     = $items->product_url;
                $pro['sku']             = $items->sku;
                $pro['has_video_shopping'] = $items->has_video_shopping;
                $pro['stock_status']    = $items->stock_status;
                $pro['is_featured']     = $items->is_featured;
                $pro['is_best_selling'] = $items->is_best_selling;
                $pro['is_new']          = $items->is_new;
                $pro['sale_prices']     = $salePrices;
                $pro['mrp_price']       = $items->price;
                $pro['image']           = $items->base_image;

                $imagePath              = $items->base_image;

                if (!Storage::exists($imagePath)) {
                    $path               = asset('userImage/no_Image.jpg');
                } else {
                    $url                = Storage::url($imagePath);
                    $path               = asset($url);
                }

                $pro['image']           = $path;

                $tmp[] = $pro;
            }
        }

        if( $total < $limit  ) {
            $to = $total;
        }

        return array('products' => $tmp, 'total_count' => $total, 'from' => ($total == 0 ? '0' : '1'), 'to' => $to);
    }

    public function getProductBySlug(Request $request)
    {
        
        $product_url = $request->product_url;
        $items = Product::where('product_url', $product_url)->first();

        $category               = $items->productCategory;
        $salePrices             = getProductPrice($items);
        
        $pro                    = [];
        $pro['id']              = $items->id;
        $pro['product_name']    = $items->product_name;
        $pro['category_name']   = $category->name ?? '';
        $pro['category_slug']   = $category->slug ?? '';
        $pro['parent_category_name']   = $category->parent->name ?? '';
        $pro['parent_category_slug']   = $category->parent->slug ?? '';
        $pro['brand_name']      = $items->productBrand->brand_name ?? '';
        $pro['hsn_code']        = $items->hsn_code;
        $pro['product_url']     = $items->product_url;
        $pro['sku']             = $items->sku;
        $pro['has_video_shopping'] = $items->has_video_shopping;
        $pro['stock_status']    = $items->stock_status;
        $pro['is_featured']     = $items->is_featured;
        $pro['is_best_selling'] = $items->is_best_selling;
        $pro['is_new']          = $items->is_new;
        $pro['sale_prices']     = $salePrices;
        $pro['mrp_price']       = $items->price;
        $pro['videolinks']      = $items->productVideoLinks;
        $pro['links']           = $items->productLinks;
        $pro['image']           = $items->base_image;


        $imagePath              = $items->base_image;

        if (!Storage::exists($imagePath)) {
            $path               = asset('userImage/no_Image.jpg');
        } else {
            $url                = Storage::url($imagePath);
            $path               = asset($url);
        }

        $pro['image']                   = $path;

        $pro['description']             = $items->description;
        $pro['technical_information']   = $items->technical_information;
        $pro['feature_information']     = $items->feature_information;
        $pro['specification']           = $items->specification;
        $pro['brochure_upload']         = $items->brochure_upload;
        $pro['gallery']                 = $items->productImages;

        // $attributes = [];
        // if( isset( $items->productAttributes ) && !empty( $items->productAttributes ) ) {
        //     foreach ( $items->productAttributes as $att) {
                
        //     }
        // }

        $pro['attributes']              = $items->productAttributes;
        $related_arr                    = [];
        if( isset( $items->productRelated ) && !empty( $items->productRelated ) ) {
            foreach ($items->productRelated as $related ) {
                
                $productInfo            = Product::find($related->to_product_id);
                $category               = $productInfo->productCategory;
                $salePrices1            = getProductPrice($productInfo);
                
                $tmp2                    = [];
                $tmp2['id']              = $productInfo->id;
                $tmp2['product_name']    = $productInfo->product_name;
                $tmp2['category_name']   = $category->name ?? '';
                $tmp2['brand_name']      = $productInfo->productBrand->brand_name ?? '';
                $tmp2['hsn_code']        = $productInfo->hsn_code;
                $tmp2['product_url']     = $productInfo->product_url;
                $tmp2['sku']             = $productInfo->sku;
                $tmp2['has_video_shopping'] = $productInfo->has_video_shopping;
                $tmp2['stock_status']    = $productInfo->stock_status;
                $tmp2['is_featured']     = $productInfo->is_featured;
                $tmp2['is_best_selling'] = $productInfo->is_best_selling;
                $tmp2['is_new']          = $productInfo->is_new;
                $tmp2['sale_prices']     = $salePrices1;
                $tmp2['mrp_price']       = $productInfo->price;
                $tmp2['image']           = $productInfo->base_image;

                $imagePath              = $productInfo->base_image;

                if (!Storage::exists($imagePath)) {
                    $path               = asset('userImage/no_Image.jpg');
                } else {
                    $url                = Storage::url($imagePath);
                    $path               = asset($url);
                }

                $tmp2['image']           = $path;
                $related_arr[]          = $tmp2;
            }
        }
        $pro['related_products']    = $related_arr;
        
        return $pro;

    }
}
