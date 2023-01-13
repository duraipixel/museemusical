<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Master\OrderStatus;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Payment;
use App\Models\Product\Product;
use Exception;
use Illuminate\Http\Request;
use Razorpay\Api\Api;

class CheckoutController extends Controller
{
    public function proceedCheckout(Request $request)
    {

        $keyId = env('RAZORPAY_KEY');
        $keySecret = env('RAZORPAY_SECRET' );
        
        /***
         * 
         * 1.insert in order table with status init
         * 2.INSERT IN Order Products
         * 
         */
        $order_status           = OrderStatus::where('status', 'published')->where('order', 1)->first();

        $customer_id            = $request->customer_id;
        $cart_total             = $request->cart_total;
        $cart_items             = $request->cart_items;
        $shipping_address       = $request->shipping_address;
        
        $shipping_amount        = 0;
        $discount_amount        = 0;
        $coupon_amount          = 0;
        $pay_amount             = filter_var($request->cart_total['total'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        $order_ins['customer_id'] = $customer_id;
        $order_ins['order_no'] = getOrderNo();
        $order_ins['shipping_options'] = '1'; //not sure what this field
        $order_ins['shipping_type'] = 'Free';
        $order_ins['amount'] = $pay_amount;
        $order_ins['tax_amount'] = $cart_total['tax_total'];
        $order_ins['tax_percentage'] = $cart_total['tax_percentage'];
        $order_ins['shipping_amount'] = $shipping_amount;
        $order_ins['discount_amount'] = $discount_amount;
        $order_ins['coupon_amount'] = $coupon_amount;
        $order_ins['coupon_code'] = '';
        $order_ins['sub_total'] = $cart_total['product_tax_exclusive_total'];
        $order_ins['description'] = '';
        $order_ins['order_status_id'] = $order_status->id;
        $order_ins['status'] = 'pending';

        $order_id = Order::create($order_ins)->id;

        if (isset($cart_items) && !empty($cart_items)) {
            foreach ($cart_items as $item ) {
                
                $items_ins['order_id'] = $order_id;
                $items_ins['product_id'] = $item['id'];
                $items_ins['product_name'] = $item['product_name'];
                $items_ins['sku'] = $item['sku'];
                $items_ins['quantity'] = $item['quantity'];
                $items_ins['price'] = $item['price'];
                $items_ins['tax_amount'] = $item['gstAmount'] ?? 0;
                $items_ins['sub_total'] = $item['sub_total'];

                OrderProduct::create($items_ins);
            }
        }

        try{

            $api = new Api($keyId, $keySecret);
            $orderData = [
                    'receipt'         => $order_id,
                    'amount'          => $pay_amount * 100, 
                    'currency'        => "INR",
                    'payment_capture' => 1 // auto capture
                ];

            $razorpayOrder = $api->order->create($orderData);
            $razorpayOrderId = $razorpayOrder['id'];
            
            session()->put('razorpay_order_id', $razorpayOrderId);

            $amount = $orderData['amount'];
            $displayCurrency        = "INR";
            $data = [
                "key"               => $keyId,
                "amount"            => round($amount),
                "currency"          => "INR",
                "name"              => 'Musee Musical',		
                "image"             => asset(gSetting('logo')),
                "description"       => "Secure Payment",
                "prefill"           => [
                    "name"              => $shipping_address['name'],
                    "email"             => $shipping_address['email'],
                    "contact"           => $shipping_address['mobile_no'],
                    ],
                "notes"             => [
                    "address"           => "",
                    "merchant_order_id" => $order_id,
                    ],
                "theme"             => [
                    "color"             => "#F37254"
                    ],
                "order_id"          => $razorpayOrderId,                
            ];

            $order_info = Order::find( $order_id );
            $order_info->payment_response_id = $razorpayOrderId;
            $order_info->save();

            return $data;
        } catch(Exception $e)
        {
            dd( $e );
        }   
        
    }

    public function verifySignature(Request $request)
    {
        

        $keyId = env('RAZORPAY_KEY');
        $keySecret = env('RAZORPAY_SECRET' );

        $customer_id = $request->customer_id;

        
        $razor_response = $request->razor_response;
        $status = $request->status;

		$success = true;
		$error_message = "Payment Success";
        
		if ( isset( $razor_response['razorpay_payment_id'] ) && empty($razor_response['razorpay_payment_id']) === false)
		{
            $razorpay_order_id = $razor_response['razorpay_order_id'];
            $razorpay_signature = $razor_response['razorpay_signature'];
            // $razorpay_order_id = session()->get('razorpay_order_id');
            
			$api = new Api($keyId, $keySecret);
		    $finalorder = $api->order->fetch( $razorpay_order_id);
            
			try
			{
			     $attributes = array(
					'razorpay_order_id' => $razorpay_order_id,
					'razorpay_payment_id' => $razor_response['razorpay_payment_id'],
					'razorpay_signature' => $razor_response['razorpay_signature']
				);

				$api->utility->verifyPaymentSignature($attributes);
			}
			catch(SignatureVerificationError $e)
			{
				$success = false;
				$error_message = 'Razorpay Error : ' . $e->getMessage();
			}
           
            if( $success ) {
                Cart::where('customer_id', $customer_id)->delete();
                /** 
                 *  1. do quantity update in product
                 *  2. update order status and payment response
                 *  3. insert in payment entry 
                 */
                $order_info = Order::where('payment_response_id', $razorpay_order_id)->first();
                if( $order_info ) {
                    $order_status    = OrderStatus::where('status', 'published')->where('order', 2)->first();

                    $order_info->status = 'completed';
                    $order_info->order_status_id = $order_status->id;

                    $order_info->save();
                
                $order_items = OrderProduct::where('order_id', $order_info->id )->get();
                    if( isset( $order_items ) && !empty( $order_items ) ) {
                        foreach ($order_items as $product) {
                            $product_info = Product::find( $product->id );
                            $pquantity = $product_info->quantity- $product->quantity;
                            $product_info->quantity = $pquantity;
                            if( $pquantity == 0 ) {
                                $product_info->stock_status = 'out_of_stock';
                            }
                            $product_info->save();
                            
                        }
                    }
                    $pay_ins['order_id'] = $order_info->id;
                    $pay_ins['payment_no'] = $razor_response['razorpay_payment_id'];
                    $pay_ins['amount'] = $order_info->amount;
                    $pay_ins['paid_amount'] = $order_info->amount;
                    $pay_ins['payment_type'] = 'razorpay';
                    $pay_ins['payment_mode'] = 'online';
                    $pay_ins['response'] = serialize($finalorder);
                    $pay_ins['status'] = $finalorder['status'];

                    Payment::create($pay_ins);
                }
            }
           
		} else{	
            $success = false;
            $error_message = 'Payment Failed';

            if(isset($request->razor_response['error']) && !empty( $request->razor_response['error'] ) )
            {
                
                $orderdata = $request->razor_response['error']['metadata'];
                $razorpay_payment_id = $orderdata['payment_id'];
                $razorpay_order_id = $orderdata['order_id'];

                $api = new Api($keyId, $keySecret);

                $finalorder = $api->order->fetch( $orderdata['order_id'] );	

                $order_info = Order::where('payment_response_id', $razorpay_order_id)->first();

                if( $order_info ) {

                    $order_status    = OrderStatus::where('status', 'published')->where('order', 3)->first();

                    $order_info->status = 'cancelled';
                    $order_info->order_status_id = $order_status->id;

                    $order_info->save();
                
                    $order_items = OrderProduct::where('order_id', $order_info->id )->get();

                    if( isset( $order_items ) && !empty( $order_items ) ) {
                        foreach ($order_items as $product) {
                            $product_info = Product::find( $product->id );
                            $pquantity = $product_info->quantity- $product->quantity;
                            $product_info->quantity = $pquantity;
                            if( $pquantity == 0 ) {
                                $product_info->stock_status = 'out_of_stock';
                            }
                            $product_info->save();
                            
                        }
                    }

                    $pay_ins['order_id'] = $order_info->id;
                    $pay_ins['payment_no'] = $razorpay_payment_id;
                    $pay_ins['amount'] = $order_info->amount;
                    $pay_ins['paid_amount'] = $order_info->amount;
                    $pay_ins['payment_type'] = 'razorpay';
                    $pay_ins['payment_mode'] = 'online';
                    $pay_ins['description'] = $request->razor_response['error']['description'];
                    $pay_ins['response'] = serialize($finalorder);
                    $pay_ins['status'] = 'failed';

                    $error_message = $request->razor_response['error']['description'];

                    Payment::create($pay_ins);
                }
                
            }					
		}

        return  array( 'success' => $success, 'message' => $error_message );
    }
}
