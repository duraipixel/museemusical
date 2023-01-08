<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $customer_id = $request->customer_id;
        $guest_token = $request->guest_token;
        $product_id = $request->product_id;
        $quantity = $request->quantity;
        $type       = $request->type;
        /**
         *1.check customer id and product exist if not insert
         *2. if exist update quantiy 
         * 
         */

        $checkCart = Cart::where('customer_id', $customer_id)->where('product_id', $product_id)->first();
        $productInfo = Product::find($product_id);
        $salePrices = getProductPrice($productInfo);
        $price = $salePrices['price_original'] * $quantity;
        
        if (isset($checkCart) && !empty($checkCart)) {
            if ($type == 'delete') {
                $checkCart->delete();
            } else {
                $product_quantity = ($quantity > 1 ? $quantity : ($checkCart->quantity + $quantity));
                $checkCart->quantity    = $product_quantity;
                $checkCart->sub_total = $product_quantity * $checkCart->price;
                $checkCart->update();
                $ins['quantity'] = $checkCart->quantity;
                $ins['message'] = 'updated';
            }
        } else {
            $ins['customer_id'] = $request->customer_id;
            $ins['product_id'] = $request->product_id;
            $ins['quantity'] = $request->quantity ?? 1;
            $ins['price'] = $salePrices['price_original'];
            $ins['sub_total'] = $salePrices['price_original'] * $request->quantity ?? 1;
            Cart::create($ins);
            $ins['message'] = 'added';
        }
        return $ins;
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
