<?php

namespace App\Http\Controllers;

use App\Models\Product\ProductCategory;
use Illuminate\Http\Request;

class CommonController extends Controller
{
    public function getProductCategoryList(Request $request)
    {
        $category_id            = $request->id;
        $productCategory        = ProductCategory::where('status', 'published')->get();
        return view('platform.product.form.parts._category', compact('productCategory', 'category_id'));
    }
}
