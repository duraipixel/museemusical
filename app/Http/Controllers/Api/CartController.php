<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartAddress;
use App\Models\CartShiprocketResponse;
use App\Models\Master\Customer;
use App\Models\Master\CustomerAddress;
use App\Models\Product\Product;
use App\Models\Settings\Tax;
use App\Models\ShippingCharge;
use App\Services\ShipRocketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CartController extends Controller
{
    public function addToCart(Request $request, ShipRocketService $service)
    {
        
        $customer_id = $request->customer_id;
        $guest_token = $request->guest_token;
        $product_id = $request->id;
        $quantity = $request->quantity ?? 1;
        $type = $request->type;
        
        /**
         * 1. check customer id and product exist if not insert
         * 2. if exist update quantiy
         */

        $product_info = Product::find($product_id);
        $checkCart = Cart::when( $customer_id != '', function($q) use($customer_id) {
                            $q->where('customer_id', $customer_id);
                        })->
                        when( $customer_id == '' && $guest_token != '', function($q) use($guest_token) {
                            $q->where('token', $guest_token);
                        })->where('product_id', $product_id)->first();

        $getCartToken = Cart::when( $customer_id != '', function($q) use($customer_id) {
                            $q->where('customer_id', $customer_id);
                        })->
                        when( $customer_id == '' && $guest_token != '', function($q) use($guest_token) {
                            $q->where('token', $guest_token);
                        })->first();

        $salePrices = $request->sale_prices;
        // dd($salePrices['price_original']);
        
        if (isset($checkCart) && !empty($checkCart)) {
            if ($type == 'delete') {
                $checkCart->delete();
            } else {
                $product_quantity = $checkCart->quantity + $quantity;
                if ($product_info->quantity <= $product_quantity) {
                    $product_quantity = $product_info->quantity;
                }
                
                $checkCart->quantity  = $product_quantity;
                $checkCart->sub_total = $product_quantity * $checkCart->price;
                $checkCart->update();
            }
        } else {
            $customer_info = Customer::find($request->customer_id);
            
            if( isset( $customer_info ) && !empty( $customer_info) || !empty($request->guest_token) ) {

                if ($product_info->quantity <= $quantity) {
                    $quantity = $product_info->quantity;
                }
                $ins['customer_id']     = $request->customer_id;
                $ins['product_id']      = $product_id;
                $ins['guest_token']     = $getCartToken->guest_token ?? 'ORD' . date('ymdhis');
                $ins['quantity']        = $quantity ?? 1;
                $ins['price']           = (float)$salePrices['price_original'];
                $ins['sub_total']       = $salePrices['price_original'] * $quantity ?? 1;
                $ins['token']           = $request->guest_token ?? null;
                
                $cart_id = Cart::create($ins)->id;
                $ins['message']         = 'added';
            } else {
                return array('error' => 1, 'message' => 'Customer Data not available.Contact Administrator');
            }
        }
        return $this->getCartListAll($customer_id, null, $guest_token);
    }

    public function updateCart(Request $request, ShipRocketService $service)
    {

        $cart_id        = $request->cart_id;
        $guest_token    = $request->guest_token;
        $customer_id    = $request->customer_id;
        $quantity       = $request->quantity ?? 1;
        $customer_info = Customer::find($request->customer_id);
            
        if( isset( $customer_info ) && !empty( $customer_info) || !empty($request->guest_token) ) {
            $checkCart      = Cart::where('id', $cart_id)->first();
            // $service->getShippingRocketOrderDimensions($checkCart->customer_id);
            // dd( 'service');
    
            $checkCart->quantity = $quantity;
            $checkCart->sub_total = $checkCart->price * $quantity;
            $checkCart->update();
    
            $shiprocket_charges = $service->getShippingRocketOrderDimensions( $customer_id, $service->getToken(), $guest_token );       
    
            return $this->getCartListAll($checkCart->customer_id, null, $guest_token);
        } else {
            return array('error' => 1, 'message' => 'Customer Data not available.Contact Administrator');
        }
    }

    public function deleteCart(Request $request)
    {
        $cart_id        = $request->cart_id;
        $customer_id = $request->customer_id;
        $guest_token    = $request->guest_token;
        $checkCart      = Cart::find($cart_id);
        if( $checkCart ) {
            $checkCart->delete();
        }
        return $this->getCartListAll($customer_id, null, $guest_token);
    }

    public function clearCart(Request $request)
    {

        $customer_id        = $request->customer_id;
        $guest_token        = $request->guest_token;

        Cart::when( $customer_id != '', function($q) use($customer_id) {
                $q->where('customer_id', $customer_id);
            })->
            when( $customer_id == '' && $guest_token != '', function($q) use($guest_token) {
                $q->where('token', $guest_token);
            })->delete();

        if( $customer_id ) {
            CartAddress::where('customer_id', $customer_id)->delete();
        }
        // CartShiprocketResponse::where('cart_token')
        return $this->getCartListAll($customer_id , null, $guest_token);
    }

    public function getCarts(Request $request)
    {
        $guest_token = $request->guest_token;
        $customer_id    = $request->customer_id;
        $selected_shipping = $request->selected_shipping ?? '';
        return $this->getCartListAll($customer_id, null, $guest_token, null, $selected_shipping); 
    }

    function getCartListAll($customer_id = null, $shipping_info = null, $guest_token = null, $shipping_type = null, $selected_shipping = null, $coupon_data = null)
    {
        // dd( $coupon_data );
        $checkCart          = Cart::when( $customer_id != '', function($q) use($customer_id) {
                                        $q->where('customer_id', $customer_id);
                                    })->
                                    when( $customer_id == '' && $guest_token != '', function($q) use($guest_token) {
                                        $q->where('token', $guest_token);
                                    })->get();

        $tmp                = [];
        $grand_total        = 0;
        $tax_total          = 0;
        $product_tax_exclusive_total = 0;
        $tax_percentage = 0;
        $cartTmp = [];
        // dd( $checkCart );
        if (isset($checkCart) && !empty($checkCart)) {
            foreach ($checkCart as $citems) {
                $items = $citems->products;
                $tax = [];
                $tax_percentage = 0;

                $category               = $items->productCategory;
                $salePrices             = getProductPrice($items);

                if (isset($category->parent->tax_id) && !empty($category->parent->tax_id)) {
                    $tax_info = Tax::find($category->parent->tax_id);
                } else if (isset($category->tax_id) && !empty($category->tax_id)) {
                    $tax_info = Tax::find($category->tax_id);
                }
                // dump( $citems );
                if (isset($tax_info) && !empty($tax_info)) {
                    $tax_calculate_price = $salePrices['price_original'] * $citems->quantity;
                    $tax = getAmountExclusiveTax($tax_calculate_price, $tax_info->pecentage);
                    $tax_total =  $tax_total + ($tax['gstAmount']) ?? 0;
                    $product_tax_exclusive_total = $product_tax_exclusive_total + $tax['basePrice'];
                    // print_r( $product_tax_exclusive_total );
                    $tax_percentage         = $tax['tax_percentage'] ?? 0;
                } else {
                    $product_tax_exclusive_total = $product_tax_exclusive_total + $citems->sub_total;
                }

                $pro                    = [];
                $pro['id']              = $items->id;
                $pro['tax']             = $tax;
                $pro['tax_percentage']  = $tax_percentage;
                $pro['hsn_no']          = $items->hsn_code ?? null;
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
                $pro['max_quantity']    = $items->quantity;
                $imagePath              = $items->base_image;

                if (!Storage::exists($imagePath)) {
                    $path               = asset('assets/logo/product-noimg.jpg');
                } else {
                    $url                = Storage::url($imagePath);
                    $path               = asset($url);
                }

                $pro['image']           = $path;
                $pro['customer_id']     = $customer_id;
                $pro['guest_token']     = $citems->token;
                $pro['cart_id']         = $citems->id;
                $pro['price']           = $citems->price;
                $pro['quantity']        = $citems->quantity;
                $pro['sub_total']       = $citems->sub_total;
                $pro['shiprocket_order_id'] = $citems->guest_token;
                $grand_total            += $citems->sub_total;
                $cartTmp[] = $pro;
                
            }

            $tmp['carts'] = $cartTmp;
            $tmp['cart_count'] = count($cartTmp);
            if (isset($shipping_info) && ( !empty($shipping_info) && $shipping_type != 'flat' )  || (isset( $selected_shipping ) && !empty( $selected_shipping ) && $shipping_type != 'flat' ) ) {
                $tmp['selected_shipping_fees'] = array(
                                                'shipping_id' => $shipping_info->id ?? $selected_shipping['shipping_id'],
                                                'shipping_charge_order' => $shipping_info->charges ?? $selected_shipping['shipping_charge_order'],
                                                'shipping_type' => $shipping_type ?? $selected_shipping['shipping_type'] ?? 'fees'
                                                );
                
                $grand_total                = $grand_total + ($shipping_info->charges ?? $selected_shipping['shipping_charge_order'] ?? 0);
            } else if( $shipping_type == 'flat' && $shipping_info ) {
                $tmp['selected_shipping_fees'] = array(
                        'shipping_id' => $shipping_info['flat_charge'],
                        'shipping_charge_order' => $shipping_info['flat_charge'],
                        'shipping_type' => $shipping_type
                    );
                $grand_total                = $grand_total + ($shipping_info['flat_charge'] ?? 0);
            }
            if( isset( $coupon_data ) && !empty( $coupon_data ) ) {
                $grand_total = $grand_total - $coupon_data[0]['discount_amount'] ??$coupon_data['discount_amount'] ?? 0;
            }

            $amount         = filter_var($grand_total, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $charges        = ShippingCharge::where('status', 'published')->where('minimum_order_amount', '<', $amount)->get();
            

            $tmp['shipping_charges']    = $charges;
            $tmp['cart_total']          = array(
                'total' => number_format(round($grand_total), 2),
                'product_tax_exclusive_total' => number_format(round($product_tax_exclusive_total), 2),
                'product_tax_exclusive_total_without_format' => round($product_tax_exclusive_total),
                'tax_total' => number_format(round($tax_total), 2),
                'tax_percentage' => number_format(round($tax_percentage), 2),
                'shipping_charge' => $shipping_info->charges ?? $shipping_info['flat_charge'] ?? 0
            );
        }
        // dd( $tmp );die;
        return $tmp;
    }

    public function getShippingCharges(Request $request)
    {
        $customer_id    = $request->customer_id;

        $amount         = filter_var($request->amount, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $charges        = ShippingCharge::where('status', 'published')->where('minimum_order_amount', '<', $amount)->get();
        return $charges;
    }

    public function updateCartAmount(Request $request)
    {

        $customer_id    = $request->customer_id;
        $shipping_id    = $request->shipping_id;
        $type    = $request->type;
        $coupon_data = $request->coupon_data ?? '';
        
        
        if( isset( $type ) && !empty( $type ) && $type == 'rocket' ) {
            $cartInfo = Cart::where('customer_id', $customer_id)->first();
            
            if( isset($cartInfo->rocketResponse->shipping_charge_response_data ) && !empty( $cartInfo->rocketResponse->shipping_charge_response_data ) ) {
                $response = json_decode($cartInfo->rocketResponse->shipping_charge_response_data);
                $tmp = [];
                if( isset( $response->data->available_courier_companies ) && !empty( $response->data->available_courier_companies ) ) {
                    foreach ($response->data->available_courier_companies as $tiem) {
                        if( $tiem->id == $shipping_id ) {
                            $tmp = $tiem;
                            break;
                        }
                    }
                }
                if( $tmp ) {

                    $amount = array( 
                        (float)$tmp->coverage_charges,
                        (float)$tmp->freight_charge,
                        (float)$tmp->rate,
                        (float)$tmp->rto_charges
                    );
                    $shipping_info['charges'] = getSecondLevelCharges( $amount );
                    $shipping_info['id'] = $shipping_id;
                    $shipping_info = (object)$shipping_info;
                }
            }
        } else if( isset( $type ) && !empty( $type ) && $type == 'flat' ) {
            $shipping_info = array('flat_charge' => $shipping_id );
        } else {

            $shipping_info  = ShippingCharge::find($shipping_id);
        }

        
        return $this->getCartListAll($customer_id, $shipping_info, null, $type, null, $coupon_data);
    }

    public function getShippingRocketCharges(Request $request, ShipRocketService $service)
    {

        $from_type = $request->from_type;
        $address = $request->address;
        $shippingAddress = CustomerAddress::find($address);
        $customer_id = $request->customer_id;
        
        $cart_info = Cart::where('customer_id', $customer_id)->first();
        /**
         * get volume metric value for kg
         */
        $all_cart = Cart::where('customer_id', $customer_id)->get();
        $flat_charges = [];
        $overall_flat_charges = 0;
        // dd( $all_cart );
        if( isset( $all_cart ) && !empty( $all_cart ) ) {
            foreach ( $all_cart as $item ) {
                
                $flat_charges[] = getVolumeMetricCalculation( $item->products->productMeasurement->length ?? 0, $item->products->productMeasurement->width ?? 0, $item->products->productMeasurement->hight ?? 0 );

            }
        }
        if( !empty( $flat_charges ) ) {

            $volume_metric_weight = max($flat_charges);
            $overall_flat_charges = $volume_metric_weight * gSetting('flat_charge') ?? 0;
        }
        
        /**
         *  End Metric value calculation
         */
        if( isset( $from_type ) && !empty( $from_type ) ) {

            CartAddress::where('customer_id', $request->customer_id)
                            ->where('address_type', $from_type)->delete();
            $ins_cart = [];
            $ins_cart['cart_token'] = $cart_info->guest_token;
            $ins_cart['customer_id'] = $customer_id;
            $ins_cart['address_type'] = $from_type;
            $ins_cart['name'] = $shippingAddress->name;
            $ins_cart['email'] = $shippingAddress->email;
            $ins_cart['mobile_no'] = $shippingAddress->mobile_no;
            $ins_cart['address_line1'] = $shippingAddress->address_line1;
            $ins_cart['country'] = 'india';
            $ins_cart['post_code'] = $shippingAddress->post_code;
            $ins_cart['state'] = $shippingAddress->state;
            $ins_cart['city'] = $shippingAddress->city;

            CartAddress::create($ins_cart);
            // $data = $service->getShippingRocketOrderDimensions($customer_id, $cart_info->guest_token ?? null);
        }

        return array( 'shiprocket_charges' => $data ?? [], 'flat_charge' => round($overall_flat_charges) );

    }
}
