<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BannerResource;
use App\Http\Resources\BrandResource;
use App\Http\Resources\DiscountCollectionResource;
use App\Http\Resources\HistoryVideoResource;
use App\Http\Resources\ProductCollectionResource;
use App\Http\Resources\TestimonialResource;
use App\Models\Banner;
use App\Models\Master\Brands;
use App\Models\Offers\Coupons;
use App\Models\Product\ProductCollection;
use App\Models\Testimonials;
use App\Models\WalkThrough;
use Illuminate\Http\Request;

class CommonController extends Controller
{

    public function getAllTestimonials()
    {
        return TestimonialResource::collection(Testimonials::select('id', 'title', 'image', 'short_description', 'long_description')->where(['status' => 'published'])->orderBy('order_by', 'asc')->get());
    }

    public function getAllHistoryVideo()
    {
        return HistoryVideoResource::collection(WalkThrough::select('id', 'title', 'video_url', 'file_path', 'description')->where(['status' => 'published'])->orderBy('order_by', 'asc')->get());        
    }

    public function getAllBanners()
    {
        return BannerResource::collection(Banner::select('id', 'title', 'description', 'banner_image', 'tag_line', 'order_by')->where(['status' => 'published'])->orderBy('order_by', 'asc')->get());        
    }

    public function getAllBrands()
    {
        return BrandResource::collection(Brands::select('id', 'brand_name', 'brand_banner', 'brand_logo', 'short_description', 'notes')->where(['status' => 'published'])->orderBy('order_by', 'asc')->get());        
    }

    public function getDiscountCollections()
    {
        return DiscountCollectionResource::collection(Coupons::where(['is_discount_on' => 'yes', 'status' => 'published', 'calculate_type' => 'percentage'])->whereDate('start_date', '<=', date('Y-m-d'))->whereDate('end_date', '>=', date('Y-m-d'))->orderBy('order_by', 'asc')->get());        
    }

    public function getProductCollections(Request $request)
    {
        $order_by = $request->order_by;

        $details = ProductCollection::where(['show_home_page' => 'yes', 'status' => 'published', 'can_map_discount' => 'no'])
                    ->when($order_by != '', function($q) use($order_by) { 
                        return $q->where('order_by', $order_by);
                    })
                    ->orderBy('order_by', 'asc')->get();
        
        return ProductCollectionResource::collection($details);        
    }

}
