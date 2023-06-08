<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\OrderMail;
use App\Models\Cart;
use App\Models\CartShiprocketResponse;
use App\Models\GlobalSettings;
use App\Models\Master\Customer;
use App\Models\Master\CustomerAddress;
use App\Models\Master\EmailTemplate;
use App\Models\Master\OrderStatus;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderProduct;
use App\Models\Payment;
use App\Models\Product\Product;
use App\Models\ShippingCharge;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Razorpay\Api\Api;
use PDF;
use Mail;

class CheckoutController extends Controller
{
    public function proceedCheckout(Request $request)
    {

        $keyId = env('RAZORPAY_KEY');
        $keySecret = env('RAZORPAY_SECRET');
        // dd( $request->all() );
        /***
         * Check order product is out of stock before proceed, if yes remove from cart and notify user
         * 1.insert in order table with status init
         * 2.INSERT IN Order Products          
         */
        $order_status           = OrderStatus::where('status', 'published')->where('order', 1)->first();
        $customer_id            = $request->customer_id;
        $cart_total             = $request->cart_total;
        $cart_items             = $request->cart_items;
        $shipping_address       = $request->shipping_address;
        $billing_address        = $request->billing_address;
        $selected_shipping_fees = $request->selected_shipping_fees ?? '';
        
        #check product is out of stock
        $errors                 = [];
        if (!$shipping_address) {
            $message     = 'Shipping address not selected';
            $error = 1;
            $response['error'] = $error;
            $response['message'] = $message;
        }

        if (isset($cart_items) && !empty($cart_items)) {
            foreach ($cart_items as $item) {
                $product_id     = $item['id'];
                $cart_id        = $item['cart_id'];
                $product_info   = Product::find($product_id);
                if ($product_info->quantity < $item['quantity']) {

                    $errors[]     = $item['product_name'] . ' is out of stock, Product will be removed from cart.Please choose another';
                    $response['error'] = $errors;
                }
            }
        }
        /***
         * 1. get Shipping address
         * 2. get Billing Address
         * */
        $shipppingAddressInfo = CustomerAddress::find($shipping_address);
        $billingAddressInfo = CustomerAddress::find($billing_address);
        // dd( $selected_shipping_fees );
        $shippingCharges = [];
        $shipping_fee_id = $selected_shipping_fees['shipping_id'] ?? '';

        if (isset($cart_id) && isset($selected_shipping_fees) && !empty($selected_shipping_fees) && ( $selected_shipping_fees['shipping_type'] != 'fees' && $selected_shipping_fees['shipping_type'] != 'flat' )) {
            $cartInfo = Cart::find($cart_id);
            $cart_token = $cartInfo->guest_token;
            $shipmentResponse = CartShiprocketResponse::where('cart_token', $cart_token)->first();
            if (isset($shipmentResponse->shipping_charge_response_data) && !empty($shipmentResponse->shipping_charge_response_data)) {
                $shipChargeResponse = json_decode($shipmentResponse->shipping_charge_response_data);
                foreach ($shipChargeResponse->data->available_courier_companies as $items) {
                    if ($items->id == $shipping_fee_id) {
                        $shippingCharges = $items;
                    }
                }
            }
        }

        if (!empty($errors)) {

            $error = 1;
            $response['error'] = $error;
            $response['message'] = implode(',', $errors);

            return $response;
        }

        $shipping_amount        = 0;
        $discount_amount        = 0;
        $coupon_amount          = 0;
        $pay_amount             = filter_var($request->cart_total['total'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        $flat_type = [];
        if( isset($selected_shipping_fees) && !empty($selected_shipping_fees) && $selected_shipping_fees['shipping_type'] == 'flat') {
            $flat_type = $selected_shipping_fees;
            $shipping_amount = $selected_shipping_fees['shipping_charge_order'];
        } else {

            $shipping_type_info = ShippingCharge::find($shipping_fee_id);
    
            if (!$shipping_type_info) {
                /**
                 * check shiprocket data is available
                 */
                $shipping_amount = $cart_total['shipping_charge'];
            }

        }


        $order_ins['customer_id'] = $customer_id;
        $order_ins['order_no'] = getOrderNo();
        $order_ins['shipping_options'] = $shipping_fee_id;
        $order_ins['shipping_type'] = $shippingCharges->courier_name ?? $shipping_type_info->shipping_title ?? $flat_type['shipping_type'] ?? 'Free';
        $order_ins['amount'] = $pay_amount;
        $order_ins['tax_amount'] = str_replace(',', '', $cart_total['tax_total']);
        $order_ins['tax_percentage'] = $cart_total['tax_percentage'];
        $order_ins['shipping_amount'] = $shipping_type_info->charges ?? $shipping_amount;
        $order_ins['discount_amount'] = $discount_amount;
        $order_ins['coupon_amount'] = isset($cart_total['coupon_amount']) && !empty( $cart_total['coupon_amount'] ) ? str_replace(',', '', $cart_total['coupon_amount']) : 0;
        $order_ins['coupon_code'] = $cart_total['coupon_code'] ?? '';
        $order_ins['sub_total'] = str_replace(',', '', $cart_total['product_tax_exclusive_total']);
        $order_ins['description'] = '';
        $order_ins['order_status_id'] = $order_status->id;
        $order_ins['status'] = 'pending';
        $order_ins['billing_name'] = $billingAddressInfo['name'] ?? $shipppingAddressInfo['name'];
        $order_ins['billing_email'] = $billingAddressInfo['email'] ?? $shipppingAddressInfo['email'];
        $order_ins['billing_mobile_no'] = $billingAddressInfo['mobile_no'] ?? $shipppingAddressInfo['mobile_no'];
        $order_ins['billing_address_line1'] = $billingAddressInfo['address_line1'] ?? $shipppingAddressInfo['address_line1'];
        $order_ins['billing_address_line2'] = $billingAddressInfo['address_line2'] ?? $shipppingAddressInfo['address_line2'] ?? null;
        $order_ins['billing_landmark'] = $billingAddressInfo['landmark'] ?? $shipppingAddressInfo['landmark'] ?? null;
        $order_ins['billing_country'] = $billingAddressInfo['country'] ?? $shipppingAddressInfo['country'] ?? null;
        $order_ins['billing_post_code'] = $billingAddressInfo['post_code'] ?? $shipppingAddressInfo['post_code'] ?? null;
        $order_ins['billing_state'] = $billingAddressInfo['state'] ?? $shipppingAddressInfo['state'] ?? null;
        $order_ins['billing_city'] = $billingAddressInfo['city'] ?? $shipppingAddressInfo['city'] ?? null;

        $order_ins['shipping_name'] = $shipppingAddressInfo['name'];
        $order_ins['shipping_email'] = $shipppingAddressInfo['email'];
        $order_ins['shipping_mobile_no'] = $shipppingAddressInfo['mobile_no'];
        $order_ins['shipping_address_line1'] = $shipppingAddressInfo['address_line1'];
        $order_ins['shipping_address_line2'] = $shipppingAddressInfo['address_line2'] ?? null;
        $order_ins['shipping_landmark'] = $shipppingAddressInfo['landmark'] ?? null;
        $order_ins['shipping_country'] = $shipppingAddressInfo['country'] ?? null;
        $order_ins['shipping_post_code'] = $shipppingAddressInfo['post_code'];
        $order_ins['shipping_state'] = $shipppingAddressInfo['state'] ?? null;
        $order_ins['shipping_city'] = $shipppingAddressInfo['city'] ?? null;
        $order_ins['rocket_charge_response'] = json_encode($shippingCharges);
        $order_ins['rocket_charge_name'] = $shippingCharges->courier_name ?? null;

        $order_id = Order::create($order_ins)->id;

        if (isset($cart_items) && !empty($cart_items)) {
            foreach ($cart_items as $item) {

                $items_ins['order_id'] = $order_id;
                $items_ins['product_id'] = $item['id'];
                $items_ins['product_name'] = $item['product_name'];
                $items_ins['hsn_code'] = $item['hsn_code'];
                $items_ins['sku'] = $item['sku'];
                $items_ins['quantity'] = $item['quantity'];
                $items_ins['price'] = $item['price'];
                $items_ins['mrp_price'] = $item['sale_prices']['strike_rate_original'] ?? 0;
                $items_ins['discount_price'] = percentageAmountOnly($item['sale_prices']['strike_rate_original'], $item['sale_prices']['overall_discount_percentage']);
                $items_ins['discount_percentage'] = $item['sale_prices']['overall_discount_percentage'] ?? 0;
                $items_ins['tax_amount'] = $item['tax']['gstAmount'] ?? 0;
                $items_ins['tax_percentage'] = $item['tax_percentage'] ?? $cart_total['tax_percentage'] ?? 0;
                $items_ins['sub_total'] = $item['sub_total'];

                OrderProduct::create($items_ins);
            }
        }

        try {

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
                    "name"              => $shipppingAddressInfo['name'],
                    "email"             => $shipppingAddressInfo['email'],
                    "contact"           => $shipppingAddressInfo['mobile_no'],
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

            $order_info = Order::find($order_id);
            $order_info->payment_response_id = $razorpayOrderId;
            $order_info->save();

            return $data;
        } catch (Exception $e) {
            dd($e);
        }
    }

    public function verifySignature(Request $request)
    {

        $keyId = env('RAZORPAY_KEY');
        $keySecret = env('RAZORPAY_SECRET');

        $customer_id = $request->customer_id;

        $razor_response = $request->razor_response;
        $status = $request->status;

        $success = true;
        $error_message = "Payment Success";

        if (isset($razor_response['razorpay_payment_id']) && empty($razor_response['razorpay_payment_id']) === false) {
            $razorpay_order_id = $razor_response['razorpay_order_id'];
            $razorpay_signature = $razor_response['razorpay_signature'];
            // $razorpay_order_id = session()->get('razorpay_order_id');

            $api = new Api($keyId, $keySecret);
            $finalorder = $api->order->fetch($razorpay_order_id);

            try {
                $attributes = array(
                    'razorpay_order_id' => $razorpay_order_id,
                    'razorpay_payment_id' => $razor_response['razorpay_payment_id'],
                    'razorpay_signature' => $razor_response['razorpay_signature']
                );

                $api->utility->verifyPaymentSignature($attributes);
            } catch (SignatureVerificationError $e) {
                $success = false;
                $error_message = 'Razorpay Error : ' . $e->getMessage();
            }

            if ($success) {

                Cart::where('customer_id', $customer_id)->delete();
                /** 
                 *  1. do quantity update in product
                 *  2. update order status and payment response
                 *  3. insert in payment entry 
                 */
                $order_info = Order::where('payment_response_id', $razorpay_order_id)->first();
                if ($order_info) {
                    $order_status    = OrderStatus::where('status', 'published')->where('order', 2)->first();

                    $order_info->status = 'placed';
                    $order_info->order_status_id = $order_status->id;

                    $order_info->save();

                    $order_items = OrderProduct::where('order_id', $order_info->id)->get();

                    if (isset($order_items) && !empty($order_items)) {
                        foreach ($order_items as $product) {
                            $product_info = Product::find($product->product_id);
                            $pquantity = $product_info->quantity - $product->quantity;
                            $product_info->quantity = $pquantity;
                            if ($pquantity == 0) {
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

                    /**** order history */
                    $his['order_id'] = $order_info->id;
                    $his['action'] = 'Order Placed';
                    $his['description'] = 'Order has been placed successfully';
                    OrderHistory::create($his);

                    /****
                     * 1.send email for order placed
                     * 2.send sms for notification
                     */
                    #generate invoice
                    $globalInfo = GlobalSettings::first();
                    $pdf = PDF::loadView('platform.invoice.index', compact('order_info', 'globalInfo'));
                    Storage::put('public/invoice_order/' . $order_info->order_no . '.pdf', $pdf->output());
                    #send mail
                    $emailTemplate = EmailTemplate::select('email_templates.*')
                        ->join('sub_categories', 'sub_categories.id', '=', 'email_templates.type_id')
                        ->where('sub_categories.slug', 'new-order')->first();

                    $globalInfo = GlobalSettings::first();

                    $extract = array(
                        'name' => $order_info->billing_name,
                        'regards' => $globalInfo->site_name,
                        'company_website' => '',
                        'company_mobile_no' => $globalInfo->site_mobile_no,
                        'company_address' => $globalInfo->address,
                        'dynamic_content' => '',
                        'order_id' => $order_info->order_no
                    );
                    $templateMessage = $emailTemplate->message;
                    $templateMessage = str_replace("{", "", addslashes($templateMessage));
                    $templateMessage = str_replace("}", "", $templateMessage);
                    extract($extract);
                    eval("\$templateMessage = \"$templateMessage\";");

                    $title = $emailTemplate->title;
                    $title = str_replace("{", "", addslashes($title));
                    $title = str_replace("}", "", $title);
                    eval("\$title = \"$title\";");

                    $filePath = 'storage/invoice_order/' . $order_info->order_no . '.pdf';
                    $send_mail = new OrderMail($templateMessage, $title, $filePath);
                    // return $send_mail->render();
                    Mail::to($order_info->billing_email)->send($send_mail);

                    #send sms for notification
                    $sms_params = array(
                        'name' => $order_info->billing_name,
                        'order_no' => $order_info->order_no,
                        'amount' => $order_info->amount,
                        'payment_through' => 'Razorpay online payment',
                        'mobile_no' => [$order_info->billing_mobile_no]
                    );
                    sendMuseeSms('new_order', $sms_params);

                    #send sms for notification
                    $sms_params = array(
                        'company_name' => env('APP_NAME'),
                        'order_no' => $order_info->order_no,
                        'reference_no' => '',
                        'mobile_no' => [$order_info->billing_mobile_no]
                    );
                    sendMuseeSms('confirm_order', $sms_params);
                }
            }
        } else {
            $success = false;
            $error_message = 'Payment Failed';

            if (isset($request->razor_response['error']) && !empty($request->razor_response['error'])) {

                $orderdata = $request->razor_response['error']['metadata'];
                $razorpay_payment_id = $orderdata['payment_id'];
                $razorpay_order_id = $orderdata['order_id'];

                $api = new Api($keyId, $keySecret);

                $finalorder = $api->order->fetch($orderdata['order_id']);

                $order_info = Order::where('payment_response_id', $razorpay_order_id)->first();

                if ($order_info) {

                    $order_status    = OrderStatus::where('status', 'published')->where('order', 3)->first();

                    $order_info->status = 'payment_pending';
                    $order_info->order_status_id = $order_status->id;

                    $order_info->save();

                    $order_items = OrderProduct::where('order_id', $order_info->id)->get();

                    if (isset($order_items) && !empty($order_items)) {
                        foreach ($order_items as $product) {
                            $product_info = Product::find($product->id);
                            $pquantity = $product_info->quantity - $product->quantity;
                            $product_info->quantity = $pquantity;
                            if ($pquantity == 0) {
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

                    // /**** order history */
                    // $his['order_id'] = $order_info->id;
                    // $his['action'] = 'Order Placed';
                    // $his['description'] = 'Order has been placed successfully';
                    // OrderHistory::create($his);
                }
            }
        }

        return  array('success' => $success, 'message' => $error_message);
    }

    public function proceedCod(Request $request)
    {

        /***
         * Check order product is out of stock before proceed, if yes remove from cart and notify user
         * 1.insert in order table with status init
         * 2.INSERT IN Order Products          
         */
        
        $order_status           = OrderStatus::where('status', 'published')->where('order', 1)->first();
        $customer_id            = $request->customer_id;
        $cart_total             = $request->cart_total;
        $cart_items             = $request->cart_items;
        $shipping_address       = $request->shipping_address;
        $billing_address        = $request->billing_address;
        $selected_shipping_fees = $request->selected_shipping_fees ?? '';

        #check product is out of stock
        $errors                 = [];
        if (!$shipping_address) {
            $message     = 'Shipping address not selected';
            $error = 1;
            $response['error'] = $error;
            $response['message'] = $message;
        }

        if (isset($cart_items) && !empty($cart_items)) {
            foreach ($cart_items as $item) {
                $product_id     = $item['id'];
                $cart_id        = $item['cart_id'];
                $product_info   = Product::find($product_id);
                if ($product_info->quantity < $item['quantity']) {

                    $errors[]     = $item['product_name'] . ' is out of stock, Product will be removed from cart.Please choose another';
                    $response['error'] = $errors;
                }
            }
        }
        /***
         * 1. get Shipping address
         * 2. get Billing Address
         * */
        $shipppingAddressInfo = CustomerAddress::find($shipping_address);
        $billingAddressInfo = CustomerAddress::find($billing_address);
     
        $shippingCharges = [];
        $shipping_fee_id = $selected_shipping_fees['shipping_id'] ?? '';

        if (isset($cart_id) && isset($selected_shipping_fees) && !empty($selected_shipping_fees) && ( $selected_shipping_fees['shipping_type'] != 'fees' && $selected_shipping_fees['shipping_type'] != 'flat' ) ) {
            $cartInfo = Cart::find($cart_id);
            $cart_token = $cartInfo->guest_token;
            $shipmentResponse = CartShiprocketResponse::where('cart_token', $cart_token)->first();
            if (isset($shipmentResponse->shipping_charge_response_data) && !empty($shipmentResponse->shipping_charge_response_data)) {
                $shipChargeResponse = json_decode($shipmentResponse->shipping_charge_response_data);
                foreach ($shipChargeResponse->data->available_courier_companies as $items) {
                    if ($items->id == $shipping_fee_id) {
                        $shippingCharges = $items;
                    }
                }
            }
        }
        

        if (!empty($errors)) {

            $error = 1;
            $response['error'] = $error;
            $response['message'] = implode(',', $errors);

            return $response;
        }

        $shipping_amount        = 0;
        $discount_amount        = 0;
        $coupon_amount          = 0;
        $pay_amount             = filter_var($request->cart_total['total'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        $flat_type = [];
        if( isset($selected_shipping_fees) && !empty($selected_shipping_fees) && $selected_shipping_fees['shipping_type'] == 'flat') {
            $flat_type = $selected_shipping_fees;
            $shipping_amount = $selected_shipping_fees['shipping_charge_order'];
        } else {

            $shipping_type_info = ShippingCharge::find($shipping_fee_id);
    
            if (!$shipping_type_info) {
                /**
                 * check shiprocket data is available
                 */
                $shipping_amount = $cart_total['shipping_charge'];
            }
        }

        $customer_info = Customer::find($customer_id);

        $order_ins['customer_id'] = $customer_id;
        $order_ins['order_no'] = getOrderNo();
        $order_ins['shipping_options'] = $shipping_fee_id;
        $order_ins['shipping_type'] = $shippingCharges->courier_name ?? $shipping_type_info->shipping_title ?? $flat_type['shipping_type'] ?? 'Free';
        $order_ins['amount'] = $pay_amount;
        $order_ins['tax_amount'] = str_replace(',', '', $cart_total['tax_total']);
        $order_ins['tax_percentage'] = $cart_total['tax_percentage'];
        $order_ins['shipping_amount'] = $shipping_type_info->charges ?? $shipping_amount;
        $order_ins['discount_amount'] = $discount_amount;
        $order_ins['coupon_amount'] = isset($cart_total['coupon_amount']) && !empty( $cart_total['coupon_amount'] ) ? str_replace(',', '', $cart_total['coupon_amount']) : 0;
        $order_ins['coupon_code'] = $cart_total['coupon_code'] ?? '';
        $order_ins['sub_total'] = str_replace(',', '', $cart_total['product_tax_exclusive_total']);
        $order_ins['description'] = '';
        $order_ins['order_status_id'] = $order_status->id;
        $order_ins['status'] = 'pending';
        $order_ins['billing_name'] = $billingAddressInfo['name'] ?? $shipppingAddressInfo['name'];
        $order_ins['billing_email'] = $billingAddressInfo['email'] ?? $shipppingAddressInfo['email'] ?? $customer_info->email ?? null;
        $order_ins['billing_mobile_no'] = $billingAddressInfo['mobile_no'] ?? $shipppingAddressInfo['email'];
        $order_ins['billing_address_line1'] = $billingAddressInfo['address_line1'] ?? $shipppingAddressInfo['address_line1'];
        $order_ins['billing_address_line2'] = $billingAddressInfo['address_line2'] ?? $shipppingAddressInfo['address_line2'] ?? null;
        $order_ins['billing_landmark'] = $billingAddressInfo['landmark'] ?? $shipppingAddressInfo['landmark'] ?? null;
        $order_ins['billing_country'] = $billingAddressInfo['country'] ?? $shipppingAddressInfo['country'] ?? null;
        $order_ins['billing_post_code'] = $billingAddressInfo['post_code'] ?? $shipppingAddressInfo['post_code'] ?? null;
        $order_ins['billing_state'] = $billingAddressInfo['state'] ?? $shipppingAddressInfo['state'] ?? null;
        $order_ins['billing_city'] = $billingAddressInfo['city'] ?? $shipppingAddressInfo['city'] ?? null;

        $order_ins['shipping_name'] = $shipppingAddressInfo['name'];
        $order_ins['shipping_email'] = $shipppingAddressInfo['email'] ?? $customer_info->email ?? null;
        $order_ins['shipping_mobile_no'] = $shipppingAddressInfo['mobile_no'];
        $order_ins['shipping_address_line1'] = $shipppingAddressInfo['address_line1'];
        $order_ins['shipping_address_line2'] = $shipppingAddressInfo['address_line2'] ?? null;
        $order_ins['shipping_landmark'] = $shipppingAddressInfo['landmark'] ?? null;
        $order_ins['shipping_country'] = $shipppingAddressInfo['country'] ?? null;
        $order_ins['shipping_post_code'] = $shipppingAddressInfo['post_code'];
        $order_ins['shipping_state'] = $shipppingAddressInfo['state'] ?? null;
        $order_ins['shipping_city'] = $shipppingAddressInfo['city'] ?? null;
        $order_ins['rocket_charge_response'] = json_encode($shippingCharges);
        $order_ins['rocket_charge_name'] = $shippingCharges->courier_name ?? null;
        $order_ins['is_cod'] = 'yes';

        $order_id = Order::create($order_ins)->id;

        if (isset($cart_items) && !empty($cart_items)) {
            foreach ($cart_items as $item) {

                $items_ins['order_id'] = $order_id;
                $items_ins['product_id'] = $item['id'];
                $items_ins['product_name'] = $item['product_name'];
                $items_ins['hsn_code'] = $item['hsn_code'];
                $items_ins['sku'] = $item['sku'];
                $items_ins['quantity'] = $item['quantity'];
                $items_ins['price'] = $item['price'];
                $items_ins['mrp_price'] = $item['sale_prices']['strike_rate_original'] ?? 0;
                $items_ins['discount_price'] = percentageAmountOnly($item['sale_prices']['strike_rate_original'], $item['sale_prices']['overall_discount_percentage']);
                $items_ins['discount_percentage'] = $item['sale_prices']['overall_discount_percentage'] ?? 0;
                $items_ins['tax_amount'] = $item['tax']['gstAmount'] ?? 0;
                $items_ins['tax_percentage'] = $cart_total['tax_percentage'] ?? $item['tax_percentage'] ?? 0;
                $items_ins['sub_total'] = $item['sub_total'];

                OrderProduct::create($items_ins);
            }
        }

        Cart::where('customer_id', $customer_id)->delete();
        /** 
         *  1. do quantity update in product
         *  2. update order status and payment response
         *  3. insert in payment entry 
         */
        
        $order_info = Order::find($order_id);
        if ($order_info) {

            $pay_ins['order_id'] = $order_info->id;
            $pay_ins['amount'] = $order_info->amount;
            $pay_ins['paid_amount'] = 0;
            $pay_ins['payment_type'] = 'cod';
            $pay_ins['payment_mode'] = 'online';
            $pay_ins['response'] = null;
            $pay_ins['status'] = 'pending';

            Payment::create($pay_ins);

            $order_status    = OrderStatus::where('status', 'published')->where('order', 2)->first();

            $order_info->status = 'placed';
            $order_info->order_status_id = $order_status->id;

            $order_info->save();

            $order_items = OrderProduct::where('order_id', $order_info->id)->get();

            if (isset($order_items) && !empty($order_items)) {
                foreach ($order_items as $product) {
                    $product_info = Product::find($product->product_id);
                    $pquantity = $product_info->quantity - $product->quantity;
                    $product_info->quantity = $pquantity;
                    if ($pquantity == 0) {
                        $product_info->stock_status = 'out_of_stock';
                    }
                    $product_info->save();
                }
            }
         

            /**** order history */
            $his['order_id'] = $order_info->id;
            $his['action'] = 'Order Placed';
            $his['description'] = 'Order has been placed with cash on delivery successfully';
            OrderHistory::create($his);

            /****
             * 1.send email for order placed
             * 2.send sms for notification
             */
            #generate invoice
            $globalInfo = GlobalSettings::first();
            $pdf = PDF::loadView('platform.invoice.index', compact('order_info', 'globalInfo'));
            Storage::put('public/invoice_order/' . $order_info->order_no . '.pdf', $pdf->output());
            #send mail
            $emailTemplate = EmailTemplate::select('email_templates.*')
                ->join('sub_categories', 'sub_categories.id', '=', 'email_templates.type_id')
                ->where('sub_categories.slug', 'new-order')->first();

            $globalInfo = GlobalSettings::first();

            $extract = array(
                'name' => $order_info->billing_name,
                'regards' => $globalInfo->site_name,
                'company_website' => '',
                'company_mobile_no' => $globalInfo->site_mobile_no,
                'company_address' => $globalInfo->address,
                'dynamic_content' => '',
                'order_id' => $order_info->order_no
            );
            $templateMessage = $emailTemplate->message;
            $templateMessage = str_replace("{", "", addslashes($templateMessage));
            $templateMessage = str_replace("}", "", $templateMessage);
            extract($extract);
            eval("\$templateMessage = \"$templateMessage\";");

            $title = $emailTemplate->title;
            $title = str_replace("{", "", addslashes($title));
            $title = str_replace("}", "", $title);
            eval("\$title = \"$title\";");

            $filePath = 'storage/invoice_order/' . $order_info->order_no . '.pdf';
            $send_mail = new OrderMail($templateMessage, $title, $filePath);
            // return $send_mail->render();
            Mail::to($order_info->billing_email)->send($send_mail);

            #send sms for notification
            // $sms_params = array(
            //     'name' => $order_info->billing_name,
            //     'order_no' => $order_info->order_no,
            //     'amount' => $order_info->amount,
            //     'payment_through' => 'Cash on delivery',
            //     'mobile_no' => [$order_info->billing_mobile_no]
            // );
            // sendMuseeSms('new_order', $sms_params);

            #send sms for notification
            $sms_params = array(
                'company_name' => env('APP_NAME'),
                'order_no' => $order_info->order_no,
                'reference_no' => '',
                'mobile_no' => [$order_info->billing_mobile_no]
            );
            sendMuseeSms('confirm_order', $sms_params);
            $success = '1';
            $message = 'Cash on Delivery order placed successfully';
        }

        return  array('success' => $success, 'message' => $message);
    }
}
