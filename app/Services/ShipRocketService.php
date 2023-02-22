<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartAddress;
use App\Models\CartShiprocketResponse;
use App\Models\Master\Customer;
use App\Models\Settings\Tax;
use Illuminate\Support\Facades\DB;

class ShipRocketService
{
    public $token;
    public $email;
    public $password;

    public function __construct()
    {
        $this->email = 'onlinemuseemusical@gmail.com';
        $this->password = 'password123';
    }

    public function getToken()
    {

        $curl = curl_init();

        $params = array('email' => $this->email, 'password' => $this->password);

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/auth/login',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $data_response = '';
        if (!empty($response)) {
            $data_response = json_decode($response);
        }
        return $data_response->token ?? '';
        // return array('token' => $this->token, 'email' => $this->email, 'password' => $this->password);
    }

    public function rocketToken($order_id)
    {
        $CartShiprocketResponse = CartShiprocketResponse::where('cart_token', $order_id)->first();
        return  $CartShiprocketResponse->rocket_token ?? $this->getToken();
    }

    public function createOrder($params)
    {
        $token = $this->rocketToken($params['order_id']);
        $curl = curl_init();

        // $params = array(
        //     "order_id" =>  "224-4779",
        //     "order_date" =>  "2023-02-14 15:11",
        //     "pickup_location" =>  "Primary",
        //     "channel_id" =>  "",
        //     "comment" =>  "Reseller =>  M/s Goku",
        //     "billing_customer_name" =>  "Naruto",
        //     "billing_last_name" =>  "Uzumaki",
        //     "billing_address" =>  "House 221B, Leaf Village",
        //     "billing_address_2" =>  "Near Hokage House",
        //     "billing_city" =>  "New Delhi",
        //     "billing_pincode" =>  "110002",
        //     "billing_state" => "Delhi",
        //     "billing_country" => "India",
        //     "billing_email" => "naruto@uzumaki.com",
        //     "billing_phone" => "9876543210",
        //     "shipping_is_billing" => true,
        //     "shipping_customer_name" => "",
        //     "shipping_last_name" => "",
        //     "shipping_address" => "",
        //     "shipping_address_2" => "",
        //     "shipping_city" => "",
        //     "shipping_pincode" =>  "",
        //     "shipping_country" =>  "",
        //     "shipping_state" =>  "",
        //     "shipping_email" =>  "",
        //     "shipping_phone" =>  "",
        //     "order_items" =>  [
        //         [
        //             "name" =>  "Kunai",
        //             "sku" =>  "chakra123",
        //             "units" =>  10,
        //             "selling_price" =>  "900",
        //             "discount" => "",
        //             "tax" =>  "",
        //             "hsn" =>  441122
        //         ]
        //     ],
        //     "payment_method" => "Prepaid",
        //     "shipping_charges" => 0,
        //     "giftwrap_charges" => 0,
        //     "transaction_charges" => 0,
        //     "total_discount" => 0,
        //     "sub_total" =>  9000,
        //     "length" => 10,
        //     "breadth" => 15,
        //     "height" => 20,
        //     "weight" => 2.5
        // );

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/orders/create/adhoc',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        CartShiprocketResponse::where('cart_token', $params['order_id'])->delete();

        $ins_params['cart_token'] = $params['order_id'];
        $ins_params['rocket_token'] = $token;
        $ins_params['request_type'] = 'create_order';
        $ins_params['rocket_order_request_data'] = json_encode($params);
        $ins_params['rocket_order_response_data'] = $response;

        CartShiprocketResponse::create($ins_params);
    }

    public function getShippingRocketOrderDimensions($customer_id)
    {
        $checkCart = Cart::where('customer_id', $customer_id)->get();
        $customer = Customer::find($customer_id);
        $cartBillAddress = CartAddress::where('customer_id', $customer_id)->where('address_type', 'billing')->first();
        $cartShipAddress = CartAddress::where('customer_id', $customer_id)->where('address_type', 'shipping')->first();

        if ($cartBillAddress && $cartShipAddress) {

            $product_id = [];
            $cartItemsarr = [];
            $cartTotal = 0;
            if (isset($checkCart) && !empty($checkCart)) {
                foreach ($checkCart as $citems) {

                    if ($citems->products) {
                        $pro = $citems->products;

                        $product_id[] = $pro->id;
                        $tax_total  = 0;
                        $tax = [];
                        $category               = $pro->productCategory;
                        $salePrices             = getProductPrice($pro);

                        if (isset($category->parent->tax_id) && !empty($category->parent->tax_id)) {
                            $tax_info = Tax::find($category->parent->tax_id);
                        } else if (isset($category->tax_id) && !empty($category->tax_id)) {
                            $tax_info = Tax::find($category->tax_id);
                        }
                        if (isset($tax_info) && !empty($tax_info)) {
                            $tax = getAmountExclusiveTax($salePrices['price_original'], $tax_info->pecentage);
                            $tax_total =  $tax_total + ($tax['gstAmount'] * $citems->quantity) ?? 0;
                        }
                        $tmp = [];
                        $tmp['hsn']             = $pro->hsn_code ?? null;
                        $tmp['name']            = $pro->product_name;
                        $tmp['sku']             = $pro->sku;
                        $tmp['tax']             = $tax_total ?? '';
                        $tmp['discount']        = '';
                        $tmp['units']           = $citems->quantity;
                        $tmp['selling_price']   = $citems->sub_total;

                        $cartItemsarr[] = $tmp;
                        $cartTotal            += $citems->sub_total;
                    }
                }
            }

            $measure = DB::table('product_measurements')
                ->selectRaw("max(width) as width, max(hight) as height, max(length) as length, sum(weight) as weight")
                ->whereIn('product_id', $product_id)->first();

            $params = array(
                "order_id" => $checkCart[0]->guest_token,
                "order_date" => date('Y-m-d h:i'),
                "pickup_location" =>  "Primary",
                "channel_id" =>  "",
                "comment" =>  "",
                "billing_customer_name" => $cartBillAddress->name,
                "billing_last_name" =>  "",
                "billing_address" =>  $cartBillAddress->address_line1,
                "billing_address_2" => $cartBillAddress->address_line2,
                "billing_city" => $cartBillAddress->city,
                "billing_pincode" => $cartBillAddress->post_code,
                "billing_state" => $cartBillAddress->state,
                "billing_country" => "India",
                "billing_email" => $cartBillAddress->email,
                "billing_phone" => $cartBillAddress->mobile_no,
                "shipping_is_billing" => true,
                "shipping_customer_name" => $cartShipAddress->name,
                "shipping_last_name" => "",
                "shipping_address" => $cartShipAddress->address_line1,
                "shipping_address_2" => $cartShipAddress->address_line2,
                "shipping_city" => $cartShipAddress->city,
                "shipping_pincode" => $cartShipAddress->post_code,
                "shipping_country" => "India",
                "shipping_state" => $cartShipAddress->state,
                "shipping_email" => $cartShipAddress->email,
                "shipping_phone" => $cartShipAddress->mobile_no,
                "order_items" => $cartItemsarr,
                "payment_method" => "Prepaid",
                "shipping_charges" => 0,
                "giftwrap_charges" => 0,
                "transaction_charges" => 0,
                "total_discount" => 0,
                "sub_total" => $cartTotal,
                "length" => $measure->length,
                "breadth" => $measure->width,
                "height" => $measure->height,
                "weight" => $measure->weight
            );

            $this->createOrder($params);

            /**
             * get Shipping Charges
             */

            $charge_array = array(
                "pickup_postcode" => $cartBillAddress->post_code,
                "delivery_postcode" => $cartShipAddress->post_code,
                "order_id" => $params['order_id'],
                "cod" =>  false,
                "weight" => $measure->weight,
                "length" => $measure->length,
                "breadth" => $measure->width,
                "height" => $measure->height,
                "declared_value" => $cartTotal,
                "mode" => "Surface",
                "is_return" => 0,
                "couriers_type" => 0,
                "only_local" => 0
            );

            $this->getShippingCharges($charge_array);
                                    
        }
    }

    public function getShippingCharges($data)
    {
        $token = $this->rocketToken($data['order_id']);
        $curl = curl_init();

        curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/courier/serviceability/',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_POSTFIELDS => json_encode($data),
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json',
                        'Authorization: Bearer '.$token
                    ),
            ));

        $response = curl_exec($curl);
        curl_close($curl);

        echo $response;die;
    }
}
