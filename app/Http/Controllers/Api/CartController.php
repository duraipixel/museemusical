<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        
        $customer_id = $request->customer_id;
        $guest_token = $request->guest_token;
        $product_id = $request->id;
        $quantity = $request->quantity ?? 1;
        $type       = $request->type;
        
        /**
         *1.check customer id and product exist if not insert
         *2. if exist update quantiy 
         * 
         */
        
        $product_info = Product::find($product_id);
        $checkCart = Cart::where('customer_id', $customer_id)->where('product_id', $product_id)->first();
       
        $salePrices = $request->sale_prices;        
        
        if (isset($checkCart) && !empty($checkCart)) {
            if ($type == 'delete') {
                $checkCart->delete();
            } else {
                 
                $product_quantity = $checkCart->quantity + $quantity;
                if( $product_info->quantity <= $product_quantity ) {
                    $product_quantity = $product_info->quantity;
                }
                
                $checkCart->quantity    = $product_quantity;
                $checkCart->sub_total = $product_quantity * $checkCart->price;
                $checkCart->update();

            }

        } else {
            if( $product_info->quantity <= $quantity ) {
                $quantity = $product_info->quantity;
            }
            $ins['customer_id']     = $request->customer_id;
            $ins['product_id']      = $product_id;
            $ins['quantity']        = $quantity ?? 1;
            $ins['price']           = $salePrices['price_original'];
            $ins['sub_total']       = $salePrices['price_original'] * $quantity ?? 1;

            $cart_id = Cart::create($ins)->id;
            $ins['message']         = 'added';

        }
        return $this->getCartListAll($customer_id);
    }

    public function getCarts(Request $request)
    {

        $customer_id    = $request->customer_id;
        return $this->getCartListAll($customer_id);

    }

    function getCartListAll($customer_id) {
        
        $checkCart      = Cart::where('customer_id', $customer_id)->get();
        $tmp = [];
        
        if (isset($checkCart ) && !empty($checkCart )) {
            foreach ($checkCart as $citems ) {
                foreach ($citems->products as $items ) {

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
                    $pro['mrp_price']       = $items->price;
                    $pro['image']           = $items->base_image;
    
                    $imagePath              = $items->base_image;
    
                    if (!Storage::exists($imagePath)) {
                        $path               = asset('assets/logo/product-noimg.jpg');
                    } else {
                        $url                = Storage::url($imagePath);
                        $path               = asset($url);
                    }
    
                    $pro['image']           = $path;
                    $pro['customer_id']     = $customer_id;
                    $pro['cart_id']         = $citems->id;
                    $pro['price']           = $citems->price;
                    $pro['quantity']        = $citems->quantity;
                    $pro['sub_total']       = $citems->sub_total;
    
                    $tmp[] = $pro;
                }
            }

            
        }
        return $tmp;
    }

    public function deleteCart(Request $request)
    {
        $customer_id = $request->customer_id;
        if( $customer_id ) {
            Cart::where('customer_id', $customer_id)->delete();
            $ins['message'] = 'deleted';
            $ins['status'] = 'success';
        } else {
            Cart::where('customer_id', $customer_id)->delete();
            $ins['message'] = 'can not delete. customer id not found';
            $ins['status'] = 'error';
        }
        
        return $ins;
    }
}
