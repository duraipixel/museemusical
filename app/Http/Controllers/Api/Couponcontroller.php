<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Offers\CouponCategory;
use App\Models\Offers\Coupons;
use App\Models\Settings\Tax;
use App\Models\ShippingCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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

                //get all category id from cart products
            $category_usedCart = Cart::select('carts.*', 'products.product_name', 'products.category_id', DB::raw('GROUP_CONCAT(DISTINCT concat(mm_product_categories.parent_id, ",", mm_product_categories.id)) as category_array'))
                                        ->join('products', 'products.id', '=', 'carts.product_id')    
                                        ->join('product_categories', 'product_categories.id', '=', 'products.category_id')
                                        ->where('carts.customer_id', $customer_id)
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
                                    if ($cartCount) {
                                        if ($cartCount->sub_total >= $coupon->minimum_order_value) {
                                            /**
                                             * Check percentage or fixed amount
                                             */
                                            switch ($coupon->calculate_type) {

                                                case 'percentage':
                                                    $product_amount += percentageAmountOnly($cartCount->sub_total, $coupon->calculate_value);
                                                    $tmp['discount_amount'] = percentageAmountOnly($cartCount->sub_total, $coupon->calculate_value);
                                                    $tmp['product_id'] = $cartCount->product_id;
                                                    $tmp['coupon_applied_amount'] = $cartCount->sub_total;
                                                    $tmp['coupon_type']     = array('discount_type' => $coupon->calculate_type, 'discount_value' => $coupon->calculate_value);
                                                    $overall_discount_percentage += $coupon->calculate_value;
                                                    $has_product++;
                                                    $couponApplied[] = $tmp;
                                                    break;
                                                case 'fixed_amount':
                                                    $product_amount += $coupon->calculate_value;
                                                    $tmp['discount_amount'] = $coupon->calculate_value;
                                                    $tmp['product_id'] = $cartCount->product_id;
                                                    $tmp['coupon_applied_amount'] = $cartCount->sub_total;
                                                    $tmp['coupon_type']         = array('discount_type' => $coupon->calculate_type, 'discount_value' => $coupon->calculate_value);
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
                                            $response['cart_info'] = $this->getCartListAll($customer_id, $response);
                                        }
                                    } else {
                                        $has_product_error++;
                                    }
                                }
                                if ($has_product == 0 && $has_product_error > 0) {
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
                            $checkCartData = Cart::selectRaw('mm_carts.*,mm_products.product_name,mm_product_categories.name,mm_coupon_categories.id as catcoupon_id, SUM(mm_carts.sub_total) as category_total')
                                                ->join('products', 'products.id', '=', 'carts.product_id')
                                                ->join('product_categories', 'product_categories.id', '=', 'products.category_id')
                                                ->join('coupon_categories', function ($join) {
                                                    $join->on('coupon_categories.category_id', '=', 'product_categories.id');
                                                    // $join->orOn('coupon_categories.category_id', '=', 'product_categories.parent_id');
                                                })
                                                ->where('coupon_categories.coupon_id', $coupon->id )
                                                ->where('carts.customer_id', $customer_id)
                                                // ->groupBy('carts.product_id')
                                                ->first();

                            if( isset( $checkCartData) && !empty( $checkCartData ) ) {
                                
                                if ($checkCartData->category_total >= $coupon->minimum_order_value) {
                                    /**
                                     * check percentage or fixed amount
                                     */
                                    switch ($coupon->calculate_type) {

                                        case 'percentage':
                                            $product_amount = percentageAmountOnly($checkCartData->category_total, $coupon->calculate_value);
                                            $tmp['discount_amount'] = percentageAmountOnly($checkCartData->category_total, $coupon->calculate_value);
                                            $tmp['coupon_id'] = $coupon->id;
                                            $tmp['coupon_code'] = $coupon->coupon_code;
                                            $tmp['coupon_applied_amount'] = $checkCartData->category_total;
                                            $tmp['coupon_type'] = array('discount_type' => $coupon->calculate_type, 'discount_value' => $coupon->calculate_value);
                                            $overall_discount_percentage = $coupon->calculate_value;
                                            $couponApplied = $tmp;
                                            break;
                                        case 'fixed_amount':
                                            $product_amount += $coupon->calculate_value;
                                            $tmp['discount_amount'] = $coupon->calculate_value;
                                            $tmp['coupon_id'] = $coupon->id;
                                            $tmp['coupon_code'] = $coupon->coupon_code;
                                            $tmp['coupon_applied_amount'] = $checkCartData->sub_total;
                                            $tmp['coupon_type']         = array('discount_type' => $coupon->calculate_type, 'discount_value' => $coupon->calculate_value);
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
                                    $response['cart_info'] = $this->getCartListAll($customer_id, $response);
                                }
                            } else {
                                $response['status'] = 'error';
                                $response['message'] = 'Cart order does not meet coupon minimum order amount';
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

    function getCartListAll($customer_id, $couponInfo = '')
    {

        $checkCart          = Cart::where('customer_id', $customer_id)->get();
        $tmp                = ['carts'];
        $grand_total        = 0;
        $tax_total          = 0;
        $product_tax_exclusive_total = 0;
        $tax_percentage = 0;
        if (isset($checkCart) && !empty($checkCart)) {
            foreach ($checkCart as $citems) {
                // dd($citems->products->productCategory);

                $items = $citems->products;
                $tax = [];
                $category               = $items->productCategory;
                $salePrices             = getProductPrice($items);

                if (isset($category->parent->tax_id) && !empty($category->parent->tax_id)) {
                    $tax_info = Tax::find($category->parent->tax_id);
                } else if (isset($category->tax_id) && !empty($category->tax_id)) {
                    $tax_info = Tax::find($category->tax_id);
                }
                if (isset($tax_info) && !empty($tax_info)) {
                    $tax = getAmountExclusiveTax($salePrices['price_original'], $tax_info->pecentage);
                    $tax_total =  $tax_total + $tax['gstAmount'] ?? 0;
                    $product_tax_exclusive_total = $product_tax_exclusive_total + ($tax['basePrice'] ?? 0 * $citems->quantity);
                    $tax_percentage         = $tax['tax_percentage'] ?? 0;
                } else {
                    $product_tax_exclusive_total = $product_tax_exclusive_total + $citems->sub_total;
                }

                $pro                    = [];
                $pro['id']              = $items->id;
                $pro['tax']             = $tax;
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
                $pro['cart_id']         = $citems->id;
                $pro['price']           = $citems->price;
                $pro['quantity']        = $citems->quantity;
                $pro['sub_total']       = $citems->sub_total;
                $grand_total            += $citems->sub_total;
                $tmp['carts'][] = $pro;
            }

            if (isset($couponInfo) && !empty($couponInfo)) {
                $grand_total            = $grand_total - $couponInfo['coupon_amount'];
            }

            $tmp['cart_total']         = array(
                'total' => number_format(round($grand_total), 2),
                'product_tax_exclusive_total' => number_format(round($product_tax_exclusive_total), 2),
                'tax_total' => number_format(round($tax_total), 2),
                'tax_percentage' => number_format(round($tax_percentage), 2),
                'coupon_amount' => number_format($couponInfo['coupon_amount'], 2) ?? '',
                'coupon_code' => $couponInfo['coupon_code'] ?? '',
            );
            $amount         = filter_var($grand_total, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $charges        = ShippingCharge::where('status', 'published')->where('minimum_order_amount', '<', $amount)->get();

            $tmp['shipping_charges']    = $charges;
        }
        return $tmp;
    }
}
