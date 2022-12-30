<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category\SubCategory;
use App\Models\Product\ProductCollection;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    public function getFilterStaticSideMenu()
    {

        $product_availability = array( 
                                    'in_stock' => 'In Stock',
                                    'notify' => 'Upcoming',
                                    'exclude_out_of_stock' => 'Exclude Out Of Stock'
                                );

        $video_shopping         = array( 'video_shopping' => 'Video Shopping is available');

        $sory_by                = array(
                                    array( 'id' => null, 'name' => 'Featured', 'slug' => 'is_featured' ),
                                    array( 'id' => null, 'name' => 'Price: High to Low', 'slug' => 'price_high_to_low' ),
                                    array( 'id' => null, 'name' => 'Price: High to Low', 'slug' => 'price_high_to_low' ),
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

        $response               = array(
                                    'product_availability' => $product_availability,
                                    'video_shopping' => $video_shopping,
                                    'sory_by' => $sory_by,
                                    'discounts' => $discounts
                                );

        return $response;
    
    }
}
