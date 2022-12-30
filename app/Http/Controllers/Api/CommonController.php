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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
        return BrandResource::collection(Brands::select('id', 'brand_name', 'brand_banner', 'brand_logo', 'short_description', 'notes', 'slug')->where(['status' => 'published'])->orderBy('order_by', 'asc')->get());        
    }

    public function getBrandByAlphabets()
    {
        $alphas = range('A', 'Z');
        
        $checkArray = [];
        if( isset( $alphas ) && !empty( $alphas ) ) {
            foreach ( $alphas as $items ) {
                
                
                $data = Brands::where(DB::raw('SUBSTR(brand_name, 1, 1)'), strtolower($items))->get();
                $childTmp = [];
                if( isset( $data ) && !empty( $data ) ) {
                    foreach ($data as $daitem ) {
                        $tmp1                    = [];
                        $brandLogoPath           = 'brands/'.$daitem->id.'/option1/'.$daitem->brand_logo;
                        $url                     = Storage::url($brandLogoPath);
                        $path                    = asset($url);

                        $tmp1[ 'id' ]            = $daitem->id;
                        $tmp1[ 'title' ]         = $daitem->brand_name;
                        $tmp1[ 'slug' ]          = $daitem->slug;
                        $tmp1[ 'image' ]         = $path;
                        $tmp1[ 'brand_banner' ]  = $daitem->brand_banner;
                        $tmp1[ 'description' ]   = $daitem->short_description;
                        $tmp1[ 'notes' ]         = $daitem->notes;

                        $childTmp[]     = $tmp1;
                    }
                }
                $tmp[ $items ]  = $childTmp;
                $checkArray   = $tmp;  
            }
        }
        // dd( $checkArray );
        return $checkArray;
    }

    public function getDiscountCollections()
    {

        // Coupons::where(['is_discount_on' => 'yes', 'status' => 'published', 'calculate_type' => 'percentage'])->whereDate('start_date', '<=', date('Y-m-d'))->whereDate('end_date', '>=', date('Y-m-d'))->orderBy('order_by', 'asc')->get()
        
        return DiscountCollectionResource::collection([]);        
    }

    

}
