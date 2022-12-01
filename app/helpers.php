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
        $gstAmount = $productAmount - ( $productAmount * (100/(100 + $gstPercentage) ) );
        $basePrice = $productAmount - $gstAmount;
        return array('basePrice' => $basePrice, 'gstAmount' => $gstAmount );
    }
}

if (! function_exists('generateProductSku')) {
    function generateProductSku($brand) {
        $countNumber    = '0000';
        $sku = 'MM-'.date('m').'-'.strtoupper($brand).'-'.$countNumber;

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