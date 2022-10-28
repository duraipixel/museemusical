<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\TestimonialsExport;
use App\Models\Category\MainCategory;
use App\Models\Master\Brands;
use App\Models\Product\ProductCategory;
use App\Models\Testimonials;
use Illuminate\Support\Facades\DB;
use DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Auth;
use Excel;
use PDF;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $title                  = "Product";
        $breadCrum              = array('Products', 'Product');
        return view('platform.product.index', compact('title', 'breadCrum'));
    }

    public function addEditPage(Request $request)
    {
        $title                  = "Add Product";
        $breadCrum              = array('Products', 'Add Product');
        $productCategory        = ProductCategory::where('status', 'published')->get();

        $productLabels          = MainCategory::where(['slug' => 'product-labels', 'status' => 'published'])->first();
        
        $productTags            = MainCategory::where(['slug' => 'product-tags', 'status' => 'published'])->first();
        $brands                 = Brands::where('status', 'published')->get();
        
        return view('platform.product.form.add_edit_form', compact('title', 'breadCrum', 'productCategory', 'productLabels', 'productTags', 'brands' ));
    }

    public function saveForm(Request $request)
    {
        dd( $request->all() );
    }

}
