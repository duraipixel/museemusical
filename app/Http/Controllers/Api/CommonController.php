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
use App\Models\Master\State;
use App\Models\Offers\Coupons;
use App\Models\Product\Product;
use App\Models\Product\ProductCollection;
use App\Models\RecentView;
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
                        $brandLogoPath           = 'brands/'.$daitem->id.'/default/'.$daitem->brand_logo;
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

        $details        = ProductCollection::where(['show_home_page' => 'yes', 'status' => 'published', 'can_map_discount' => 'yes'])
                            ->orderBy('order_by', 'asc')->limit(4)->get();

        $collection     = [];

        if( isset( $details ) && !empty( $details ) ) {
            foreach ( $details as $item ) {
                $tmp                    = [];
                $tmp['id']              = $item->id;
                $tmp['collection_name'] = $item->collection_name;
                $tmp['slug']            = $item->slug;
                $tmp['tag_line']        = $item->tag_line;
                $tmp['order_by']        = $item->order_by;
                
                if( isset( $item->collectionProducts ) && !empty( $item->collectionProducts ) ) {
                    $i = 0;
                    foreach ( $item->collectionProducts as $proItem ) {
                        $pro                    = [];
                        if( $i == 4 ) {break;}
                        $productInfo            = Product::find( $proItem->product_id );

                        $salePrices             = getProductPrice( $productInfo );
                        
                        $pro['id']              = $productInfo->id;
                        $pro['product_name']    = $productInfo->product_name;
                        $pro['hsn_code']        = $productInfo->hsn_code;
                        $pro['product_url']     = $productInfo->product_url;
                        $pro['sku']             = $productInfo->sku;
                        $pro['has_video_shopping'] = $productInfo->has_video_shopping;
                        $pro['stock_status']    = $productInfo->stock_status;
                        $pro['is_featured']     = $productInfo->is_featured;
                        $pro['is_best_selling'] = $productInfo->is_best_selling;
                        $pro['is_new']          = $productInfo->is_new;
                        $pro['sale_prices']     = $salePrices;
                        $pro['mrp_price']       = $productInfo->price;
                        $pro['image']           = $productInfo->base_image;
                        $pro['category']        = $productInfo->ProductCategory->name ?? null;
                        $pro['category_slug']   = $productInfo->ProductCategory->slug ?? null;

                        $imagePath              = $productInfo->base_image;

                        if(!Storage::exists( $imagePath)) {
                            $path               = asset('assets/logo/no-img-1.jpg');
                        } else {
                            $url                = Storage::url($imagePath);
                            $path               = asset($url);
                        }

                        $pro['image']           = $path;

                        $tmp['products'][]      = $pro; 

                        $i++;
                        
                    }
                }

                $collection[] = $tmp;
                
                
            }
        }
        return $collection;       
    }

    public function setRecentView( Request $request)
    {
        $ins['customer_id'] = $request->customer_id;
        $product_url = $request->product_url;
        $product_info = Product::where('product_url', $product_url)->first();
        $ins['product_id'] = $product_info->id;
        RecentView::where('customer_id', $request->customer_id)->where('product_id', $product_info->id)->delete();

        RecentView::create($ins);

        return true;
    }

    public function getSates()
    {
        return State::select('state_name', 'id', 'state_code')->where('status', 1)->get();
    }

    public function getMetaInfo(Request $request)
    {
        $page = $request->page;

        switch ($page) {
            case 'profile':
                # code...
                break;
            
            default:
                # code...
                break;
        }
        
    }

    

}
