<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category\SubCategory;
use App\Models\Product\Product;
use App\Models\Product\ProductAttributeSet;
use App\Models\Product\ProductCategory;
use App\Models\Product\ProductCollection;
use App\Models\Product\ProductWithAttributeSet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FilterController extends Controller
{
    public function getFilterStaticSideMenu()
    {

        $product_availability = array(
            'in_stock' => 'In Stock',
            'coming_soon' => 'Upcoming',
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
        // $sory_by                = array_merge($tags, $labels, $sory_by);
        // $sory_by                = array_merge($tags, $labels, $sory_by);

        // $discounts              = ProductCollection::select('id', 'collection_name', 'slug')
        //     ->where('can_map_discount', 'yes')
        //     ->where('status', 'published')
        //     ->orderBy('order_by', 'asc')
        //     ->get()->toArray();

        $discounts = ProductCollection::select('id', 'collection_name', 'slug')->where(['show_home_page' => 'yes', 'status' => 'published', 'can_map_discount' => 'yes'])
            ->orderBy('order_by', 'asc')->get()->toArray();


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
            'discounts' => $discounts
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
        $filter_collection      = $request->collection;
        $filter_attribute       = $request->attributes_category;
        $sort                   = $request->sort;

        $filter_availability_array = [];
        $filter_attribute_array = [];
        $filter_brand_array = [];
        $filter_discount_array = [];
        $filter_collection_array = [];
        $filter_booking     = $request->booking;
        if (isset($filter_attribute) && !empty($filter_attribute)) {

            $filter_attribute_array = explode("-", $filter_attribute);
        }
        if (isset($filter_availability) && !empty($filter_availability)) {
            $filter_availability_array = explode("-", $filter_availability);
        }
        if (isset($filter_brand) && !empty($filter_brand)) {
            $filter_brand_array     = explode("_", $filter_brand);
        }

        if (isset($filter_discount) && !empty($filter_discount)) {
            $filter_discount_array     = explode("_", $filter_discount);
        }

        if (isset($filter_collection) && !empty($filter_collection)) {
            $filter_collection_array     = explode("_", $filter_collection);
        }

        $category_info = ProductCategory::where('slug', $filter_category)->first();
        $cat_id = $category_info->id ?? '';
        // dd( $category_info );
        $productAttrNames = [];
        if (isset($filter_attribute_array) && !empty($filter_attribute_array)) {
            $productWithData = ProductWithAttributeSet::whereIn('id', $filter_attribute_array)->get();
            if (isset($productWithData) && !empty($productWithData)) {
                foreach ($productWithData as $attr) {
                    $productAttrNames[] = $attr->title;
                }
            }
        }

        $limit = 12;
        $skip = (isset($page) && !empty($page)) ? ($page * $limit) : 0;

        $from   = 1 + ($page * $limit);


        $take_limit = $limit + ($page * $limit);
        $total = Product::select('products.*')->where('products.status', 'published')
                ->join('product_categories', function ($join) {
                    $join->on('product_categories.id', '=', 'products.category_id');
                    $join->orOn('product_categories.parent_id', '=', 'products.category_id');
                })
            ->join('brands', 'brands.id', '=', 'products.brand_id')
            ->when($filter_category != '', function ($q) use ($cat_id) {
                $q->where(function ($query) use ($cat_id) {
                    return $query->where('product_categories.id', $cat_id)->orWhere('product_categories.parent_id', $cat_id);
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
                $q->join('product_collections', 'product_collections.id', '=', 'product_collections_products.product_collection_id');
                return $q->whereIn('product_collections.slug', $filter_discount_array);
            })
            ->when($filter_collection != '', function ($q) use ($filter_collection_array) {
                $q->join('product_collections_products', 'product_collections_products.product_id', '=', 'products.id');
                $q->join('product_collections', 'product_collections.id', '=', 'product_collections_products.product_collection_id');
                return $q->whereIn('product_collections.slug', $filter_collection_array);
            })
            ->when($filter_attribute != '', function ($q) use ($productAttrNames) {
                $q->join('product_with_attribute_sets', 'product_with_attribute_sets.product_id', '=', 'products.id');
                return $q->whereIn('product_with_attribute_sets.title', $productAttrNames);
            })
            ->when($sort == 'price_high_to_low', function ($q) {
                $q->orderBy('products.price', 'desc');
            })
            ->when($sort == 'price_low_to_high', function ($q) {
                $q->orderBy('products.price', 'asc');
            })
            ->when($sort == 'is_featured', function ($q) {
                $q->orderBy('products.is_featured', 'desc');
            })
            ->where('products.stock_status', '!=', 'out_of_stock')
            ->groupBy('products.id')
            ->get();
        $total = count($total);


        $details = Product::select('products.*')->where('products.status', 'published')
            ->join('product_categories', function ($join) {
                $join->on('product_categories.id', '=', 'products.category_id');
                $join->orOn('product_categories.parent_id', '=', 'products.category_id');
            })
            ->join('brands', 'brands.id', '=', 'products.brand_id')
            ->when($filter_category != '', function ($q) use ($cat_id) {
                $q->where(function ($query) use ($cat_id) {
                    return $query->where('product_categories.id', $cat_id)->orWhere('product_categories.parent_id', $cat_id);
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
                $q->join('product_collections', 'product_collections.id', '=', 'product_collections_products.product_collection_id');
                return $q->whereIn('product_collections.slug', $filter_discount_array);
            })
            ->when($filter_collection != '', function ($q) use ($filter_collection_array) {
                $q->join('product_collections_products', 'product_collections_products.product_id', '=', 'products.id');
                $q->join('product_collections', 'product_collections.id', '=', 'product_collections_products.product_collection_id');
                return $q->whereIn('product_collections.slug', $filter_collection_array);
            })
            ->when($filter_attribute != '', function ($q) use ($productAttrNames) {
                $q->join('product_with_attribute_sets', 'product_with_attribute_sets.product_id', '=', 'products.id');
                return $q->whereIn('product_with_attribute_sets.title', $productAttrNames);
            })
            ->when($sort == 'price_high_to_low', function ($q) {
                $q->orderBy('products.mrp', 'desc');
            })
            ->when($sort == 'price_low_to_high', function ($q) {
                $q->orderBy('products.mrp', 'asc');
            })
            ->when($sort == 'is_featured', function ($q) {
                $q->orderBy('products.is_featured', 'desc');
            })
            ->where('products.stock_status', '!=', 'out_of_stock')
            ->groupBy('products.id')
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
                $pro['mrp_price']       = $items->mrp;
                $pro['image']           = $items->base_image;
                $pro['max_quantity']    = $items->quantity;

                $imagePath              = $items->base_image;

                if (!Storage::exists($imagePath)) {
                    $path               = asset('assets/logo/product-noimg.jpg');
                } else {
                    $url                = Storage::url($imagePath);
                    $path               = asset($url);
                }

                $pro['image']           = $path;

                $tmp[] = $pro;
            }
        }

        // if ($total < $limit) {
        //     $to = $total;
        // }
        $to = count($details);
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
        $pro['max_quantity']    = $items->quantity;

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
        // $pro['gallery']                 = $items->productImages;

        if (isset($items->productImages) && !empty($items->productImages)) {
            foreach ($items->productImages as $att) {

                $gallery_url            = Storage::url($att->gallery_path);
                $path                   = asset($gallery_url);

                $pro['gallery'][] = $path;
            }
        }


        $attributes = [];
        if (isset($items->productMappedAttributes) && !empty($items->productMappedAttributes)) {
            foreach ($items->productMappedAttributes as $attrItems) {
                $tmp = [];
                $tmp['id'] = $attrItems->attrInfo->id;
                $tmp['title'] = $attrItems->attrInfo->title;
                $tmp['slug'] = $attrItems->attrInfo->slug;
                $parent_sub = [];
                if (isset($attrItems->getFilterSpec) && !empty($attrItems->getFilterSpec)) {
                    foreach ($attrItems->getFilterSpec as $subitem) {
                        $sub_tmp = [];

                        $sub_tmp['id'] = $subitem->id;
                        $sub_tmp['title'] = $subitem->title;
                        $sub_tmp['value'] = $subitem->attribute_values;
                        $parent_sub[] = $sub_tmp;
                    }
                }
                $tmp['child'] = $parent_sub;

                $attributes[] = $tmp;
            }
        }
        $pro['attributes']              = $attributes;
        $related_arr                    = [];

        $productInfo            = Product::find(1340);

        if (isset($items->productRelated) && !empty($items->productRelated)) {
            foreach ($items->productRelated as $related) {

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
                    $path               = asset('assets/logo/no-img-1.jpg');
                } else {
                    $url                = Storage::url($imagePath);
                    $path               = asset($url);
                }

                $tmp2['image']           = $path;
                $related_arr[]          = $tmp2;
            }
        }
        $pro['product_extra_information'] = array(
            array('name' => 'description', 'data' => $items->specification, 'has_data' => isset($items->specification) && !empty($items->specification) ? true : false),
            array('name' => 'specification', 'data' => $attributes, 'has_data' => count($attributes) > 0 ? true : false),
            array('name' => 'media', 'data' => $items->productVideoLinks, 'has_data' => count($attributes) > 0 ? true : false),
        );

        $pro['related_products']    = $related_arr;
        $pro['meta'] = $items->productMeta;

        return $pro;
    }

    public function globalSearch(Request $request)
    {
        $search_type = $request->search_type;
        $query = $request->search_field;

        $searchData = [];
        $error = 1;
        if (!empty($query)) {

            $productInfo = Product::where(function ($qr) use ($query) {
                $qr->where('product_name', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%");
            })->where('status', 'published')->get();
            
            if (count($productInfo) == 0) {
                $productInfo = Product::where(function ($qr) use ($query) {
                    $qr->whereRaw("MATCH (mm_products.product_name) AGAINST ('" . $query . "' IN BOOLEAN MODE)")
                        ->orWhere('sku', 'like', "%{$query}%");
                })->where('status', 'published')->get();
            }

            if (isset($productInfo) && !empty($productInfo) && count($productInfo) > 0) {
                $error = 0;
                foreach ($productInfo as $items) {

                    $category               = $items->productCategory;
                    $salePrices             = getProductPrice($items);

                    $pro                    = [];
                    $pro['has_data']        = 'yes';
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
                    $pro['max_quantity']    = $items->quantity;

                    $imagePath              = $items->base_image;

                    if (!Storage::exists($imagePath)) {
                        $path               = asset('assets/logo/no-img-1.jpg');
                    } else {
                        $url                = Storage::url($imagePath);
                        $path               = asset($url);
                    }

                    $pro['image']                   = $path;
                    $searchData[] = $pro;
                }
            } else {
                $pro = [];
                $pro['has_data']        = 'no';
                $pro['message']         = 'No record found';

                $searchData[] = $pro;
            }
        }

        return array('products' => $searchData, 'status' => $error);
    }

    public function getOtherCategories(Request $request)
    {

        $category       = $request->category;

        $otherCategory   = ProductCategory::select('id', 'name', 'slug')
            ->when($category != '', function ($q) use ($category) {
                $q->where('slug', '!=', $category);
            })
            ->where(['status' => 'published', 'parent_id' => 0])
            ->orderBy('order_by', 'asc')
            ->get();
        $data = [];
        if (isset($otherCategory) && !empty($otherCategory)) {
            foreach ($otherCategory as $item) {

                $tmp = [];
                $tmp['id'] = $item->id;
                $tmp['name'] = $item->name;
                $tmp['slug'] = $item->slug;
                $tmp['description'] = $item->description;

                $imagePath              = $item->image;

                if (!Storage::exists($imagePath)) {
                    $path               = asset('assets/logo/no-img-1.jpg');
                } else {
                    $url                = Storage::url($imagePath);
                    $path               = asset($url);
                }

                $tmp['image'] = $path;

                $data[] = $tmp;
            }
        }
        return $data;
    }

    public function getDynamicFilterCategory(Request $request)
    {
        // dd( $request->all() );
        $category_slug = $request->category_slug;
        // $category_slug = 'keyboard-keyboard';
        $productCategory = ProductCategory::where('slug', $category_slug)->first();
        
        if (isset($productCategory) && !empty($productCategory)) {
            $cat_id = $productCategory->id;
            $brands = Product::select('brands.id', 'brands.brand_name', 'brands.slug')
                ->join('brands', 'brands.id', '=', 'products.brand_id')
                ->join('product_categories', function ($join) {
                    $join->on('product_categories.id', '=', 'products.category_id');
                    $join->orOn('product_categories.parent_id', '=', 'products.category_id');
                })
                ->where(function ($query) use ($cat_id) {
                    return $query->where('product_categories.id', $cat_id)->orWhere('product_categories.parent_id', $cat_id);
                })
                ->where('products.stock_status', 'in_stock')
                ->where('products.status', 'published')->groupBy('products.brand_id')
                ->get();

            $whereIn = [];
            $whereIn[] = $productCategory->id;
            if (isset($productCategory->childCategory) && !empty($productCategory->childCategory)) {
                foreach ($productCategory->childCategory  as $items) {
                    $whereIn[] = $items->id;
                }
            }

            $data = [];
            $attributes = [];
            $topLevelData = Product::select('product_attribute_sets.id', 'product_attribute_sets.title', 'product_attribute_sets.slug')
                ->whereIn('category_id', $whereIn)
                ->join('product_map_attributes', 'product_map_attributes.product_id', '=', 'products.id')
                ->join('product_attribute_sets', 'product_attribute_sets.id', '=', 'product_map_attributes.attribute_id')
                ->groupBy('title')->get();

            if (isset($topLevelData) && !empty($topLevelData)) {
                foreach ($topLevelData as $vals) {
                    $tmp = [];
                    $tmp['id'] = $vals->id;
                    $tmp['title'] = $vals->title;
                    $tmp['slug'] = $vals->slug;
                    $child = [];
                    $secondLevelData = Product::select('product_with_attribute_sets.id', 'product_with_attribute_sets.title', 'product_with_attribute_sets.attribute_values')
                        ->join('product_map_attributes', 'product_map_attributes.product_id', '=', 'products.id')
                        ->join('product_with_attribute_sets', 'product_with_attribute_sets.product_attribute_set_id', '=', 'product_map_attributes.id')
                        ->whereIn('category_id', $whereIn)
                        ->where('product_map_attributes.attribute_id', $vals->id)
                        ->groupBy('title')->get();
                    if (isset($secondLevelData) && !empty($secondLevelData)) {

                        foreach ($secondLevelData as $sec) {
                            $fValues = [];
                            $fValues['id'] = $sec->id;
                            $fValues['title'] = $sec->title;
                            // $fValues['attribute_values'] = $sec->attribute_values;

                            $filterDatas = Product::select('product_with_attribute_sets.id', 'product_with_attribute_sets.title', 'product_with_attribute_sets.attribute_values')
                                ->join('product_map_attributes', 'product_map_attributes.product_id', '=', 'products.id')
                                ->join('product_with_attribute_sets', 'product_with_attribute_sets.product_attribute_set_id', '=', 'product_map_attributes.id')
                                ->whereIn('category_id', $whereIn)
                                ->where('product_map_attributes.attribute_id', $vals->id)
                                ->where('product_with_attribute_sets.title', $sec->title)
                                ->get();
                            if (isset($filterDatas) && !empty($filterDatas)) {

                                foreach ($filterDatas as $filvalues) {
                                    $childValues = [];
                                    $childValues['id']  = $filvalues->id;
                                    $childValues['attribute_name']  = $filvalues->title;
                                    $childValues['attribute_values']  = $filvalues->attribute_values;

                                    $fValues['child'][] = $childValues;
                                }
                            }

                            $tmp['child'][] = $fValues;
                        }
                    }

                    $attributes[] = $tmp;
                }
            }

            return array('attributes' => $attributes ?? [], 'brands' => $brands ?? []);
        }
    }
}
