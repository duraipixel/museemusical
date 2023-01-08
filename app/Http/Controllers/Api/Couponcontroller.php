<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Offers\Coupons;
use Illuminate\Http\Request;

class Couponcontroller extends Controller
{
    public function applyCoupon(Request $request)
    {
        $coupon_code = $request->coupon_code;
        $customer_id = $request->customer_id;
        $carts          = Cart::where('customer_id', $customer_id)->get();
        if ($carts) {
            $coupon = Coupons::where('coupon_code', $coupon_code)
                ->where('is_discount_on', 'no')
                ->whereDate('coupons.start_date', '<=', date('Y-m-d'))
                ->whereDate('coupons.end_date', '>=', date('Y-m-d'))
                ->first();

            if (isset($coupon) && !empty($coupon)) {
                /**
                 * 1.check quantity is available to use
                 * 2.check coupon can apply for cart products
                 * 3.get percentage or fixed amount
                 * 
                 * coupon type 1- product, 2-customer, 3-category
                 */

                if ($coupon->quantity > $coupon->used_quantity ?? 0) {

                    switch ($coupon->coupon_type) {
                        case '1':
                            # product ...
                            print_r($coupon->couponProducts);
                            $has_product = 0;
                            $product_amount = 0;
                            $has_product_error = 0;
                            if (isset($coupon->couponProducts) && !empty($coupon->couponProducts)) {
                                foreach ($coupon->couponProducts as $items) {
                                    $cartCount = Cart::where('customer_id', $customer_id)->where('product_id', $items->product_id)->first();
                                    if( $cartCount ) {
                                        if( $cartCount->sub_total >= $coupon->minimum_order_value ) {
                                            /**
                                             * check percentage or fixed amount
                                             */
                                            // switch ($items->calculate_type) {

                                            //     case 'percentage':
                                            //         $strike_rate    = $price;
                                            //         $tmp['discount_amount'] = percentageAmountOnly( $price, $items->calculate_value );
                                            //         $price          = percentage( $price, $items->calculate_value );
                                            //         $discount[]         = array( 'discount_type' => $items->calculate_type, 'discount_value' => $items->calculate_value  );
                                            //         $overall_discount_percentage += $items->calculate_value;
                                            //         $has_discount   = 'yes';
                                            //         break;
                                            //     case 'fixed_amount':
                                            //         $strike_rate    = $price;
                                            //         $tmp['discount_amount'] = $items->calculate_value;
                                            //         $discount[]         = array( 'discount_type' => $items->calculate_type, 'discount_value' => $items->calculate_value  );
                                            //         $price          = $price - $items->calculate_value;
                                            //         $has_discount   = 'yes';
                                            //         break;
                                            //     default:
                                                    
                                            //         break;
                                            // }
                                             
                                        }
                                    } else {
                                        $has_product_error++;
                                    }
                                }
                                if( $has_product == 0 && $has_product_error > 0 ) {
                                    $response['status'] = 'error';
                                    $response['message'] = 'Cart order does not meet coupon minimum order amount';
                                }
                            } else {
                                $response['status'] = 'error';
                                $response['message'] = 'Coupon not applicable';
                            }
                            break;

                        case '2':
                            # customer ...
                            break;

                        case '3':
                            # category ...
                            break;

                        default:
                            # code...
                            break;
                    }
                } else {
                    $response['status'] = 'error';
                    $response['message'] = 'Coupon Limit reached';
                }
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Coupon code not available';
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = 'There is no products on the cart';
        }



        return $response;
    }
}
