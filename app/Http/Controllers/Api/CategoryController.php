<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product\ProductCategory;
use App\Models\CategoryMetaTags;

class CategoryController extends Controller
{
    public function getCategoryMeta(Request $req)
    {
        $category_slug = $req->category;
        $scategory_slug = $req->scategory;
        $category = [];
        if ($scategory_slug && $scategory_slug != null) {
            $category = ProductCategory::where('slug', $scategory_slug)->first();
        } else {
            $category = ProductCategory::where('slug', $category_slug)->first();
        }

        if ($category && $category->id) {
            $meta = CategoryMetaTags::where('category_id', $category->id)->first();
        } else {
            $meta = null;
        }

        return json_encode($meta);
    }
}
