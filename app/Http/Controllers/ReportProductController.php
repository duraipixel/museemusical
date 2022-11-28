<?php

namespace App\Http\Controllers;

use App\Models\Category\MainCategory;
use App\Models\Master\Brands;
use App\Models\Product\Product;
use App\Models\Product\ProductCategory;
use Illuminate\Http\Request;
use Datatables;

class ReportProductController extends Controller
{
    public function index(Request $request)
    {
        $title                  = "Products Report";
        $breadCrum              = array('Reports', 'Products');
        
        if ($request->ajax()) {
            
            $f_product_category = $request->get('filter_product_category');
            $f_brand = $request->get('filter_brand');
            $f_label = $request->get('filter_label');
            $f_tags = $request->get('filter_tags');
            $f_stock_status = $request->get('filter_stock_status');
            $f_product_name = $request->get('filter_product_name');
            $f_product_status = $request->get('filter_product_status');
            $f_video_booking = $request->get('filter_video_booking');
            $f_date_range = $request->get('date_range');

            $data = Product::when($f_product_category, function($q) use($f_product_category){
                return $q->where('category_id', $f_product_category);
            })
            ->when($f_brand, function($q) use($f_brand) {
                return $q->where('brand_id', $f_brand);
            })
            ->when($f_tags, function($q) use($f_tags) {
                return $q->where('tag_id', $f_tags);
            })
            ->when($f_stock_status, function($q) use($f_stock_status) {
                return $q->where('stock_status', $f_stock_status);
            })
            ->when($f_product_status, function($q) use($f_product_status) {
                return $q->where('status', $f_product_status);
            })
            ->when($f_video_booking, function($q) use($f_video_booking) {
                return $q->where('has_video_shopping', $f_video_booking);
            })
            ->when($f_product_name, function($q) use($f_product_name) {
                return $q->where(function($qr) use($f_product_name){
                    $qr->where('product_name', 'like', "%{$f_product_name}%")
                    ->orWhere('sku', 'like', "%{$f_product_name}%")
                    ->orWhere('price', 'like', "%{$f_product_name}%");
                });
            })
            ->when($f_label, function($q) use($f_label) {
                return $q->where('label_id', $f_label);
            });


            $keywords = $request->get('search')['value'];
            
            $datatables =  Datatables::of($data)
                ->filter(function ($query) use ($keywords) {
                    
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        $query->where(function($que) use($keywords, $date){
                            $que->where('has_video_shopping', 'like', "%{$keywords}%")
                                ->orWhere('status', 'like', "%{$keywords}%")
                                ->orWhere('product_name', 'like', "%{$keywords}%")
                                ->orWhere('sku', 'like', "%{$keywords}%")
                                ->orWhere('price', 'like', "%{$keywords}%")
                                ->orWhereDate("created_at", $date);
                        });
                        return $query;
                    }
                })
                ->addIndexColumn()
                ->addColumn('status', function($row){
                    $status = '<a href="javascript:void(0);" class="badge badge-light-'.(($row->status == 'published') ? 'success': 'danger').'" tooltip="Click to '.(($row->status == 'published') ? 'Unpublish' : 'Publish').'" onclick="return commonChangeStatus(' . $row->id . ', \''.(($row->status == 'published') ? 'unpublished': 'published').'\', \'products\')">'.ucfirst($row->status).'</a>';
                    return $status;
                })
                ->addColumn('category', function($row){
                    return $row->productCategory->name ?? '';
                })
                ->addColumn('brand', function($row){
                    return $row->productBrand->brand_name ?? '';
                })
               
                ->rawColumns(['status', 'category', 'brand']);
             
                return $datatables->make(true);
        }

        $addHref = route('products.add.edit');
        $routeValue = 'products';
        $productCategory        = ProductCategory::where('status', 'published')->get();
        $brands                 = Brands::where('status', 'published')->get();
        $productLabels          = MainCategory::where(['slug' => 'product-labels', 'status' => 'published'])->first();        
        $productTags            = MainCategory::where(['slug' => 'product-tags', 'status' => 'published'])->first();

        $params                 = array(
                                    'title' => $title,
                                    'breadCrum' => $breadCrum,
                                    'addHref' => $addHref,
                                    'routeValue' => $routeValue,
                                    'productCategory' => $productCategory,
                                    'brands' => $brands,
                                    'productLabels' => $productLabels,
                                    'productTags' => $productTags,
                                );

        return view('platform.reports.products.list', $params);
    }
}
