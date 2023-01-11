<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Master\Customer;
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
            } else {
                $error = 1;
                $message = 'Invalid credentials';
                $status = 'error';
                $customer_data = ''; 
            }
           
        } else {
            $error = 1;
            $message = 'Invalid credentials';
            $status = 'error';
            $customer_data = '';
        }
        return array( 'error' => $error, 'message' => $message, 'status' => $status, 'customer_data' => $customer_data );

    }
}
