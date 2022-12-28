<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductCollectionResource extends JsonResource
{
    public function toArray($request)
    {
        
        $childTmp                   = [];
        $tmp[ 'id' ]                = $this->id;
        $tmp[ 'collection_name' ]   = $this->collection_name;
        $tmp[ 'collection_slug' ]   = Str::slug($this->collection_name);
        $tmp[ 'tag_line' ]          = $this->tag_line;
        $tmp[ 'order_by' ]          = $this->order_by;
        $tmp[ 'status' ]            = $this->status;
        $tmp[ 'deleted_at' ]        = $this->deleted_at;
        $tmp[ 'updated_at' ]        = $this->updated_at;
        $tmp[ 'show_home_page' ]    = $this->show_home_page;
        if( isset($this->collectionProducts) && !empty( $this->collectionProducts )) {
            foreach ($this->collectionProducts as $items ) {
                $category = $items->product->productCategory;
                // dd( $category->id );
                $salePrices             = getProductPrice( $items->product );

                $pro                    = [];
                $pro['id']              = $items->product->id;
                $pro['product_name']    = $items->product->product_name;
                $pro['category_name']   = $category->name ?? '';
                $pro['hsn_code']        = $items->product->hsn_code;
                $pro['product_url']     = $items->product->product_url;
                $pro['sku']             = $items->product->sku;
                $pro['has_video_shopping'] = $items->product->has_video_shopping;
                $pro['stock_status']    = $items->product->stock_status;
                $pro['is_featured']     = $items->product->is_featured;
                $pro['is_best_selling'] = $items->product->is_best_selling;
                $pro['is_new']          = $items->product->is_new;
                $pro['sale_prices']     = $salePrices;
                $pro['mrp_price']       = $items->product->price;
                if( isset( $items->product->base_image ) && !empty( $items->product->base_image ) ) {
                    $url                = Storage::url($items->product->base_image);
                    $pro['image']       = asset($url);
                }

                $tmp['products'][]        = $pro;
            }
        }

        return $tmp;
    }
}
