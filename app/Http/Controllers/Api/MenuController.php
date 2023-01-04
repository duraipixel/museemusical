<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MenuAllResource;
use App\Http\Resources\MenuResource;
use App\Models\Product\ProductCategory;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    
    public function getTopMenu(Request $request)
    {
        $slug           = $request->slug;
        
        $data           = ProductCategory::where(['is_home_menu' => 'yes', 'status' => 'published'])
                            ->when( $slug != '', function($q) use($slug){
                                return $q->where('slug', $slug);
                            })
                            ->when( $slug == '', function($q) {
                                return $q->where('parent_id', 0);
                            })
                            ->orderBy('order_by', 'asc')
                            ->get();
        return MenuResource::collection($data);
        
    }

    public function getAllMenu()
    {

        $menus   = ProductCategory::select('id', 'name', 'slug')->where(['status' => 'published', 'parent_id' => 0])->orderBy('order_by', 'asc')->get();
        return MenuAllResource::collection($menus);

    }

}
