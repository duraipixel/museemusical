<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Master\Customer;
use App\Models\Master\CustomerAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function registerCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string',
            'email' => 'required|email|unique:customers,email',
            'password' => 'required|string',

        ], ['email.unique' => 'Email id is already registered.Please try to login']);
        
        if ($validator->passes()) {

            $ins['first_name'] = $request->firstName;
            $ins['email'] = $request->email;
            // $ins['mobile_no'] = $request->mobile_no;
            $ins['customer_no'] = getCustomerNo();
            $ins['password'] = Hash::make($request->password);
            $ins['status'] = 'published';

            Customer::create($ins);

            $error = 0;
            $message = 'Registered success';
            $status = 'success';
        } else {
            $error = 1;
            $message = $validator->errors()->all();
            $status = 'error';
        }
        return array( 'error' => $error, 'message' => $message, 'status' => $status );
    }

    public function doLogin(Request $request)
    {
        $email = $request->email;
        $password = $request->password;
        
        $checkCustomer = Customer::where('email', $email)->first();
        if( $checkCustomer ) {
            // dd( $password );
            if( Hash::check( $password, $checkCustomer->password ) ) {
                $error = 0;
                $message = 'Login Success';
                $status = 'success';
                $customer_data = $checkCustomer;
                $customer_address = $checkCustomer->customerAddress ?? [];
            } else {
                $error = 1;
                $message = 'Invalid credentials';
                $status = 'error';
                $customer_data = ''; 
                $customer_address = [];
            }
           
        } else {
            $error = 1;
            $message = 'Invalid credentials';
            $status = 'error';
            $customer_data = '';
            $customer_address = [];
        }
        
        return array( 'error' => $error, 'message' => $message, 'status' => $status, 'customer_data' => $customer_data, 'customer_addres' => $customer_address  );

    }

    public function addCustomerAddress(Request $request)
    {
        $ins['customer_id'] = $request->customer_id;
        $ins['address_type_id'] = $request->address_type;
        $ins['name'] = $request->contact_name;
        $ins['email'] = $request->email;
        $ins['mobile_no'] = $request->mobile_no;
        $ins['address_line1'] = $request->address;
        $ins['country'] = 'india';
        $ins['post_code'] = $request->post_code;
        $ins['state'] = $request->state;
        $ins['city'] = $request->city;

        CustomerAddress::create($ins);

        $address = CustomerAddress::where('customer_id', $request->customer_id)->get();
        return array( 'error' => 0, 'message' => 'Address added successfully', 'status' => 'success', 'customer_address' => $address );
    }

    
}
