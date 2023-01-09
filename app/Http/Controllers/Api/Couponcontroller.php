<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Offers\CouponCategory;
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
                $has_product = 0;
                $product_amount = 0;
                $has_product_error = 0;
                $overall_discount_percentage = 0;
                $couponApplied = [];
                if ($coupon->quantity > $coupon->used_quantity ?? 0) {

                    switch ($coupon->coupon_type) {
                        case '1':
                            # product ...
                            if (isset($coupon->couponProducts) && !empty($coupon->couponProducts)) {
                                foreach ($coupon->couponProducts as $items) {
                                    $cartCount = Cart::where('customer_id', $customer_id)->where('product_id', $items->product_id)->first();
                                    if( $cartCount ) {
                                        if( $cartCount->sub_total >= $coupon->minimum_order_value ) {
                                            /**
                                             * check percentage or fixed amount
                                             */
                                            switch ($coupon->calculate_type) {

                                                case 'percentage':
                                                    $product_amount += percentageAmountOnly( $cartCount->sub_total, $coupon->calculate_value );
                                                    $tmp['discount_amount'] = percentageAmountOnly( $cartCount->sub_total, $coupon->calculate_value );
                                                    $tmp['product_id'] = $cartCount->product_id;
                                                    $tmp['coupon_applied_amount'] = $cartCount->sub_total;
                                                    $tmp['coupon_type']     = array( 'discount_type' => $coupon->calculate_type, 'discount_value' => $coupon->calculate_value  );
                                                    $overall_discount_percentage += $coupon->calculate_value;
                                                    $has_product++;
                                                    $couponApplied[] = $tmp;
                                                    break;
                                                case 'fixed_amount':
                                                    $product_amount += $coupon->calculate_value;
                                                    $tmp['discount_amount'] = $coupon->calculate_value;
                                                    $tmp['product_id'] = $cartCount->product_id;
                                                    $tmp['coupon_applied_amount'] = $cartCount->sub_total;
                                                    $tmp['coupon_type']         = array( 'discount_type' => $coupon->calculate_type, 'discount_value' => $coupon->calculate_value  );
                                                    $has_product++;
                                                    $couponApplied[] = $tmp;

                                                    break;
                                                default:
                                                    
                                                    break;
                                            }

                                            $response['coupon_info'] = $couponApplied;
                                            $response['overall_applied_discount'] = $overall_discount_percentage;
                                            $response['coupon_amount'] = $product_amount;
                                            $response['coupon_id'] = $coupon->id;
                                            $response['coupon_code'] = $coupon->coupon_code;
                                            $response['status'] = 'success';
                                            $response['message'] = 'Coupon applied';
                                             
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
                            if( isset( $coupon->couponCategory ) && !empty( $coupon->couponCategory ) ) {
                                foreach ($coupon->couponCategory as $item) {
                                    $cartCount = CouponCategory::select('carts.*')->join('product_categories', 'product_categories.id', '=', 'coupon_categories.category_id')
                                                    ->join('products', function($join) {
                                                        $join->on('products.category_id', '=', 'product_categories.id');
                                                        $join->orOn('products.category_id', '=', 'product_categories.parent_id');
                                                    })->join('carts', 'carts.product_id', '=', 'products.id')
                                                        ->where('carts.customer_id', $customer_id )->first();
                                    if( $cartCount ) {
                                        if( $cartCount->sub_total >= $coupon->minimum_order_value ) {
                                            /**
                                             * check percentage or fixed amount
                                             */
                                            switch ($coupon->calculate_type) {

                                                case 'percentage':
                                                    $product_amount += percentageAmountOnly( $cartCount->sub_total, $coupon->calculate_value );
                                                    $tmp['discount_amount'] = percentageAmountOnly( $cartCount->sub_total, $coupon->calculate_value );
                                                    $tmp['product_id'] = $cartCount->product_id;
                                                    $tmp['coupon_applied_amount'] = $cartCount->sub_total;
                                                    $tmp['coupon_type']     = array( 'discount_type' => $coupon->calculate_type, 'discount_value' => $coupon->calculate_value  );
                                                    $overall_discount_percentage += $coupon->calculate_value;
                                                    $has_product++;
                                                    $couponApplied[] = $tmp;
                                                    break;
                                                case 'fixed_amount':
                                                    $product_amount += $coupon->calculate_value;
                                                    $tmp['discount_amount'] = $coupon->calculate_value;
                                                    $tmp['product_id'] = $cartCount->product_id;
                                                    $tmp['coupon_applied_amount'] = $cartCount->sub_total;
                                                    $tmp['coupon_type']         = array( 'discount_type' => $coupon->calculate_type, 'discount_value' => $coupon->calculate_value  );
                                                    $has_product++;
                                                    $couponApplied[] = $tmp;

                                                    break;
                                                default:
                                                    
                                                    break;
                                            }

                                            $response['coupon_info'] = $couponApplied;
                                            $response['overall_applied_discount'] = $overall_discount_percentage;
                                            $response['coupon_amount'] = $product_amount;
                                            $response['coupon_id'] = $coupon->id;
                                            $response['coupon_code'] = $coupon->coupon_code;
                                            $response['status'] = 'success';
                                            $response['message'] = 'Coupon applied';
                                                
                                        }
                                    } else {
                                        $has_product_error++;
                                    }
                                }
                                if( $has_product == 0 && $has_product_error > 0 ) {
                                    $response['status'] = 'error';
                                    $response['message'] = 'Cart order does not meet coupon minimum order amount';
                                }
                                
                            }
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
