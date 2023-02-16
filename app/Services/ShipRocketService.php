<?php

namespace App\Services;

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

        $params = array( 'email' => $this->email, 'password' => $this->password );

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
        return $response;
        // return array('token' => $this->token, 'email' => $this->email, 'password' => $this->password);
    }


    public function createOrder($token, $order_data)
    {

        $curl = curl_init();

        $params = array(
            "order_id" =>  "224-4779",
            "order_date" =>  "2023-02-14 15:11",
            "pickup_location" =>  "Primary",
            "channel_id" =>  "",
            "comment" =>  "Reseller =>  M/s Goku",
            "billing_customer_name" =>  "Naruto",
            "billing_last_name" =>  "Uzumaki",
            "billing_address" =>  "House 221B, Leaf Village",
            "billing_address_2" =>  "Near Hokage House",
            "billing_city" =>  "New Delhi",
            "billing_pincode" =>  "110002",
            "billing_state" =>  "Delhi",
            "billing_country" =>  "India",
            "billing_email" =>  "naruto@uzumaki.com",
            "billing_phone" =>  "9876543210",
            "shipping_is_billing" =>  true,
            "shipping_customer_name" =>  "",
            "shipping_last_name" =>  "",
            "shipping_address" =>  "",
            "shipping_address_2" =>  "",
            "shipping_city" =>  "",
            "shipping_pincode" =>  "",
            "shipping_country" =>  "",
            "shipping_state" =>  "",
            "shipping_email" =>  "",
            "shipping_phone" =>  "",
            "order_items" =>  [
                [
                    "name" =>  "Kunai",
                    "sku" =>  "chakra123",
                    "units" =>  10,
                    "selling_price" =>  "900",
                    "discount" =>  "",
                    "tax" =>  "",
                    "hsn" =>  441122
                ]
            ],
            "payment_method" => "Prepaid",
            "shipping_charges" => 0,
            "giftwrap_charges" => 0,
            "transaction_charges" => 0,
            "total_discount" => 0,
            "sub_total" =>  9000,
            "length" => 10,
            "breadth" => 15,
            "height" => 20,
            "weight" => 2.5
        );

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
                                'Authorization: Bearer '.$token
                            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;

    }
}
