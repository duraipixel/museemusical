<?php

use App\Helpers\AccessGuard;
use App\Models\Product\Product;
use App\Models\User;

if( !function_exists('gSetting') ) {
    function gSetting($column) {
        $info = \DB::table('global_settings')->first();
        if( isset( $info ) && !empty( $info ) ) {
            return $info->$column ?? '';
        } else {
            return false;
        }
    }
}

if( !function_exists('errorArrays') ) {
    function errorArrays($errors) {
        return array_map( function($err) {
            return '<div>'.str_replace(',', '', $err).'</div>';
        }, $errors);
    }
}

function sendSMS($numbers, $msg, $params) {
    extract($params);
    $uid = "museemusical";
    $pwd = urlencode("18870");
    // $Peid = "1001409933589317661";
    // $tempid = "1607100000000238808";
    $sender = urlencode("MUSEEM");

    $message = rawurlencode($msg);
    $numbers = implode(',', $numbers);
    $dtTime = date('m-d-Y h:i:s A');
    $data = "&uid=" . $uid . "&pwd=". $pwd . "&mobile=" . $numbers . "&msg=" . $message . "&sid=" .$sid. "&type=0" . "&dtTimeNow=" . $dtTime. "&entityid=" .$entityid. "&tempid=" . $tempid ;
    $ch = curl_init("http://smsintegra.com/api/smsapi.aspx?");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    echo $response;
    curl_close($ch);
    return $response;

}

if (! function_exists('access')) {
    function access() {
        return new AccessGuard();
    }
}

if (! function_exists('getAmountExclusiveTax')) {
    function getAmountExclusiveTax($productAmount, $gstPercentage) {
        
        $basePrice      = $productAmount;
        $gstAmount      = 0;
        if( (int)$gstPercentage > 0 ) {
            $gstAmount = $productAmount - ( $productAmount * (100/(100 + $gstPercentage) ) );
            $basePrice = $productAmount - $gstAmount;
        } 
       
        return array('basePrice' => $basePrice, 'gstAmount' => $gstAmount );
    }
}

if (! function_exists('generateProductSku')) {
    function generateProductSku($brand, $sku = '' ) {
        $countNumber    = '0000';
        if( empty( $sku ) ) {
            $sku = 'MM-'.date('m').'-'.strtoupper($brand).'-'.$countNumber;
        }
        

        $checkProduct = Product::where('sku', $sku)->first();
        if( isset( $checkProduct ) && !empty($checkProduct) ) {
            $old_no = $checkProduct->sku;
            $old_no = explode("-", $old_no );
            
            $end = end($old_no);
            $old_no = $end + 1;
            
            if( ( 4 - strlen($old_no) ) > 0 ){
                $new_no = '';
                for ($i=0; $i < (4 - strlen($old_no) ); $i++) { 
                    $new_no .= '0';
                }
                $ord = $new_no.$old_no;
                
                $sku =  'MM-'.date('m').'-'.strtoupper($brand).'-'.$ord;
            }
        } 
        return $sku;
    }
}

if( !function_exists('percentage') ) {
    function percentage($amount, $percent) {
        return $amount - ( $amount * ( $percent/100) );
    }
}

if( !function_exists('percentageAmountOnly') ) {
    function percentageAmountOnly($amount, $percent) {
        return ( $amount * ( $percent/100) );
    }
}

if( !function_exists('getSaleProductPrices') ) {
    function getSaleProductPrices($productsObjects, $couponsInfo) {
        
        $strike_rate    = 0;
        $price          = $productsObjects->price;
        $today          = date('Y-m-d');
        /****
         * 1.check product discount is applied via product add/edit
         * 2.check overall discount is applied for product category
         */
        $has_discount       = 'no';
        #condition 1:
        if( $today >= $productsObjects->sale_start_date && $today <= $productsObjects->sale_end_date ) {
            $strike_rate    = $productsObjects->price;
            $price          = $productsObjects->sale_price;
            $has_discount       = 'yes';
        } 

        #condition 2:
        if( $couponsInfo->quantity > $couponsInfo->used_quantity ) {
            
            #check product amount greater than minimum order value
            if( $couponsInfo->minimum_order_value <= $price ) {
                #then do percentage or fixed amount discount
                switch ($couponsInfo->calculate_type) {
                    case 'percentage':
                        $strike_rate    = $price;
                        $price          = percentage( $price, $couponsInfo->calculate_value );
                        $has_discount   = 'yes';
                        break;
                    case 'fixed_amount':
                        $strike_rate    = $price;
                        $price          = $price - $couponsInfo->calculate_value;
                        $has_discount   = 'yes';
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }

        return array( 'strike_rate' => $strike_rate, 'price' => $price, 'has_discount' => $has_discount );

    }
}

    if( !function_exists('getProductPrice') ) {
        function getProductPrice($productsObjects) {
            
            $strike_rate            = 0;
            $price                  = $productsObjects->price;
            $today                  = date('Y-m-d');
            /****
             * 1.check product discount is applied via product add/edit
             * 2.check overall discount is applied for product category
             */
            $has_discount           = 'no';
            #condition 1:
            if( $today >= $productsObjects->sale_start_date && $today <= $productsObjects->sale_end_date ) {
                $strike_rate        = $productsObjects->price;
                $price              = $productsObjects->sale_price;
                $has_discount       = 'yes';
            } 
            #condition 2:
            $getDiscountDetails     = \DB::table('coupon_categories')
                                        ->select('product_categories.name','coupons.*')
                                        ->join('coupons', 'coupons.id', '=', 'coupon_categories.coupon_id')
                                        ->join('product_categories', 'product_categories.id','=', 'coupon_categories.category_id')
                                        ->join('products', function($join) {
                                            $join->on('products.category_id', '=', 'product_categories.id');
                                            $join->orOn('products.category_id', '=', 'product_categories.parent_id');
                                        })
                                        ->where('coupons.status', 'published')
                                        ->where('is_discount_on', 'yes')
                                        ->whereDate( 'coupons.start_date', '<=', date('Y-m-d') )
                                        ->whereDate( 'coupons.end_date', '>=', date('Y-m-d') )
                                        ->where('products.id', $productsObjects->id)
                                        ->orderBy( 'coupons.order_by', 'asc' )
                                        ->get();
            
            $coupon_used            = [];
            if( isset( $getDiscountDetails ) && !empty( $getDiscountDetails ) ) {
                foreach ($getDiscountDetails as $items ) {

                    if( $items->quantity > $items->used_quantity ) {
                
                        #check product amount greater than minimum order value
                        if( $items->minimum_order_value <= $price ) {
                            #then do percentage or fixed amount discount
                            $tmp['coupon_details']  = $items;

                            switch ($items->calculate_type) {

                                case 'percentage':
                                    $strike_rate    = $price;
                                    $tmp['discount_amount'] = percentageAmountOnly( $price, $items->calculate_value );
                                    $price          = percentage( $price, $items->calculate_value );
                                    $has_discount   = 'yes';
                                    break;
                                case 'fixed_amount':
                                    $strike_rate    = $price;
                                    $tmp['discount_amount'] = $items->calculate_value;
                                    $price          = $price - $items->calculate_value;
                                    $has_discount   = 'yes';
                                    break;
                                default:
                                    
                                    break;
                            }
                            $coupon_used[]          = $tmp;
                        }
                    }
                   
                    

                }
            }
            $coupon_used['strike_rate']     = number_format($strike_rate, 2);
            $coupon_used['price']           = number_format($price,2);
            
            return $coupon_used;
    
        }
}