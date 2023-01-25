<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function getOrders(Request $request)
    {
        $customer_id        = $request->customer_id ?? 1;
        $orderAll           = Order::where('customer_id', $customer_id)->orderBy('id', 'desc')->get();
        $orders = [];
        if( isset( $orderAll ) && !empty( $orderAll ) ) {
            foreach ( $orderAll as $item ) {
                
                $tmp = [];
                $tmp['id'] = $item->id;
                $tmp['order_no'] = $item->order_no;
                $tmp['shipping_type'] = $item->shipping_type;
                $tmp['amount'] = $item->amount;
                $tmp['tax_percentage'] = $item->tax_percentage;
                $tmp['tax_amount'] = $item->tax_amount;
                $tmp['shipping_amount'] = $item->shipping_amount;
                $tmp['discount_amount'] = $item->discount_amount;
                $tmp['coupon_amount'] = $item->coupon_amount;
                $tmp['coupon_code'] = $item->coupon_code;
                $tmp['sub_total'] = $item->sub_total;
                $tmp['billing_name'] = $item->billing_name;
                $tmp['billing_email'] = $item->billing_email;
                $tmp['billing_mobile_no'] = $item->billing_mobile_no;
                $tmp['billing_address_line1'] = $item->billing_address_line1;
                $tmp['billing_address_line2'] = $item->billing_address_line2;
                $tmp['billing_landmark'] = $item->billing_landmark;
                $tmp['billing_country'] = $item->billing_country;
                $tmp['billing_post_code'] = $item->billing_post_code;
                $tmp['billing_state'] = $item->billing_state;
                $tmp['billing_city'] = $item->billing_city;
                $tmp['status'] = $item->status;
                $tmp['invoice_file'] = asset('storage/invoice_order/'.$item->order_no.'.pdf');
                $tmp['order_date'] = date( 'd M Y H:i A', strtotime( $item->created_at ));
                $itemArray = [];
                if( isset( $item->orderItems ) && !empty( $item->orderItems ) ) {
                    foreach ($item->orderItems as $pro) {

                        $tmp1 = [];
                        $tmp1['product_name'] = $pro->product_name;
                        $tmp1['hsn_code'] = $pro->hsn_code;
                        $tmp1['sku'] = $pro->sku;
                        $tmp1['quantity'] = $pro->quantity;
                        $tmp1['price'] = $pro->price;
                        $tmp1['tax_amount'] = $pro->tax_amount;
                        $tmp1['tax_percentage'] = $pro->tax_percentage;
                        $tmp1['quantity'] = $pro->quantity;
                        $tmp1['sub_total'] = $pro->sub_total;

                        $imagePath              = $pro->products->base_image;

                        if (!Storage::exists($imagePath)) {
                            $path               = asset('assets/logo/no-img-1.jpg');
                        } else {
                            $url                = Storage::url($imagePath);
                            $path               = asset($url);
                        }
                        
                        $tmp1['image']                   = $path;

                        $itemArray[] = $tmp1;
                    }
                }
                $tmp['items'] = $itemArray;
                #customers
                $tmp['customer'] = $item->customer;
                $tmp['tracking'] = $item->tracking;

                $orders[] = $tmp;
            }
        }

        return $orders;
    }
}
