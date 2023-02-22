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
use App\Models\Product\ProductCategory;
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
        if (isset($alphas) && !empty($alphas)) {
            foreach ($alphas as $items) {


                $data = Brands::where(DB::raw('SUBSTR(brand_name, 1, 1)'), strtolower($items))->get();
                $childTmp = [];
                if (isset($data) && !empty($data)) {
                    foreach ($data as $daitem) {
                        $tmp1                    = [];
                        $brandLogoPath           = 'brands/' . $daitem->id . '/default/' . $daitem->brand_logo;

                        if ($daitem->brand_logo === null) {
                            $path                = asset('assets/logo/no-img-1.jpg');
                        } else {
                            $url                 = Storage::url($brandLogoPath);
                            $path                = asset($url);
                        }

                        $tmp1['id']            = $daitem->id;
                        $tmp1['title']         = $daitem->brand_name;
                        $tmp1['slug']          = $daitem->slug;
                        $tmp1['image']         = $path;
                        $tmp1['brand_banner']  = $daitem->brand_banner;
                        $tmp1['description']   = $daitem->short_description;
                        $tmp1['notes']         = $daitem->notes;

                        $childTmp[]     = $tmp1;
                    }
                }
                $tmp[$items]  = $childTmp;
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

        if (isset($details) && !empty($details)) {
            foreach ($details as $item) {
                $tmp                    = [];
                $tmp['id']              = $item->id;
                $tmp['collection_name'] = $item->collection_name;
                $tmp['slug']            = $item->slug;
                $tmp['tag_line']        = $item->tag_line;
                $tmp['order_by']        = $item->order_by;

                if (isset($item->collectionProducts) && !empty($item->collectionProducts)) {
                    $i = 0;
                    foreach ($item->collectionProducts as $proItem) {
                        $pro                    = [];
                        if ($i == 4) {
                            break;
                        }
                        $productInfo            = Product::find($proItem->product_id);

                        $salePrices             = getProductPrice($productInfo);

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

                        if (!Storage::exists($imagePath)) {
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

    public function setRecentView(Request $request)
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

    public function getAllHomeDetails()
    {

        $details = ProductCollection::where(['show_home_page' => 'yes', 'status' => 'published', 'can_map_discount' => 'no'])
            ->orderBy('order_by', 'asc')->get();
        $response['collection'] = ProductCollectionResource::collection($details);
        $response['testimonials'] =  TestimonialResource::collection(Testimonials::select('id', 'title', 'image', 'short_description', 'long_description')->where(['status' => 'published'])->orderBy('order_by', 'asc')->get());
        $response['video'] = HistoryVideoResource::collection(WalkThrough::select('id', 'title', 'video_url', 'file_path', 'description')->where(['status' => 'published'])->orderBy('order_by', 'asc')->get());
        $response['banner'] = BannerResource::collection(Banner::select('id', 'title', 'description', 'banner_image', 'mobile_banner', 'tag_line', 'order_by')->where(['status' => 'published'])->orderBy('order_by', 'asc')->get());
        return $response;
    }

    public function getBrandInfo(Request $request)
    {

        $slug = $request->slug;
        $brand_info = Brands::where('slug', $slug)->first();

        if( isset( $brand_info->brand_banner ) && !empty( $brand_info->brand_banner ) ) {

            $bannerImagePath        = 'brands/' . $brand_info->id . '/banner/' . $brand_info->brand_banner;
            $url                    = Storage::url($bannerImagePath);
            $banner_path            = asset($url);

        } else {
            $banner_path = asset('assets/logo/no_img_category_banner.jpg');
        }

        if( isset( $brand_info->brand_logo ) && !empty( $brand_info->brand_logo ) ) {

            $logoImagePath          = 'brands/' . $brand_info->id . '/default/' . $brand_info->brand_logo;
            $url                    = Storage::url($logoImagePath);
            $logo_path              = asset($url);
        } else {
            $logo_path = null;
        }

        $response['brand_info'] = $brand_info;
        $parent['id'] = $brand_info->id;
        $parent['name'] = $brand_info->name;
        $parent['slug'] = $brand_info->slug;
        $parent['logo'] = $logo_path;
        $parent['banner'] = $banner_path;
        if ($brand_info->category) {
            foreach ($brand_info->category as $items) {
                $tmp = [];
                $tmp['id'] = $items->id;
                $tmp['name'] = $items->name;
                $tmp['slug'] = $items->slug;
                if ($items->image) {
                    $catImagePath = 'productCategory/' . $items->id . '/default/' . $items->image;
                    $url = Storage::url($catImagePath);
                    $path = asset($url);
                } else {

                    $path = asset('assets/logo/no_img_category_lg.jpg');
                }
                $tmp['image'] = $path;
                /**
                 * small images
                 */
                if ($items->image_sm) {
                    $catImagePath1 = 'productCategory/' . $items->id . '/small/' . $items->image_sm;
                    $url1 = Storage::url($catImagePath1);
                    $path1 = asset($url1);
                } else {
                    $path1 = asset('assets/logo/no_img_category_sm.jpg');
                }
                $tmp['image_sm'] = $path1;
                /**
                 * medium images
                 */
                if ($items->image_md) {
                    $catImagePath2 = 'productCategory/' . $items->id . '/medium/' . $items->image_md;
                    $url2 = Storage::url($catImagePath2);
                    $path2 = asset($url2);
                } else {
                    $path2 = asset('assets/logo/no_img_category_md.jpg');
                }
                $tmp['image_md'] = $path2;

                /**
                 * get sub category
                 */
                $sub_category_info =  ProductCategory::select('product_categories.*')->join('products', 'products.category_id', '=', 'product_categories.id')
                    ->join('brands', 'brands.id', '=', 'products.brand_id')
                    ->where('product_categories.parent_id', $items->id)
                    ->groupBy('product_categories.id')->get();
                $sub_category = [];
                if (isset($sub_category_info) && !empty($sub_category_info)) {
                    foreach ($sub_category_info as $catitem) {
                        $tmp1 = [];
                        $tmp1['id'] = $catitem->id;
                        $tmp1['name'] = $catitem->name;
                        $tmp1['slug'] = $catitem->slug;
                        if ($catitem->image) {
                            $catImagePath = 'productCategory/' . $catitem->id . '/default/' . $catitem->image;
                            $url = Storage::url($catImagePath);
                            $path1 = asset($url);
                        } else {
                            $path1 = asset('assets/logo/no_img_category_lg.jpg');
                        }
                        $tmp1['image'] = $path1;

                        /**
                         * small images
                         */
                        if ($catitem->image_sm) {
                            $catImagePath1 = 'productCategory/' . $catitem->id . '/small/' . $catitem->image_sm;
                            $url1 = Storage::url($catImagePath1);
                            $path1 = asset($url1);
                        } else {
                            $path1 = asset('assets/logo/no_img_category_sm.jpg');
                        }
                        $tmp1['image_sm'] = $path1;
                        /**
                         * medium images
                         */
                        if ($catitem->image_md) {
                            $catImagePath2 = 'productCategory/' . $catitem->id . '/medium/' . $catitem->image_md;
                            $url2 = Storage::url($catImagePath2);
                            $path2 = asset($url2);
                        } else {
                            $path2 = asset('assets/logo/no_img_category_md.jpg');
                        }
                        $tmp1['image_md'] = $path2;

                        $sub_category[] = $tmp1;
                    }
                }
                $tmp['sub_category'] = $sub_category;
                $parent['category'][] = $tmp;
            }
        }
        return $parent;
    }
}
