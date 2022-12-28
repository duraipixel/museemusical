<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCollectionResource;
use App\Models\Product\ProductCollection;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
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

    public function getProductCollectionByOrder(Request $request)
    {
        $order_by = $request->order_by;
       

        $details = ProductCollection::where(['show_home_page' => 'yes', 'status' => 'published', 'can_map_discount' => 'no'])
                    ->when($order_by != '', function($q) use($order_by) { 
                        return $q->where('order_by', $order_by);
                    })
                    ->orderBy('order_by', 'asc')->first();
        
        return ProductCollectionResource::collection($details);        
    }
}
