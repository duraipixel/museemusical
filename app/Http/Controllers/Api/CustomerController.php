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
            'first_name' => 'required|string',
            'email' => 'required|email|unique:customers,email',
            'mobile_no' => 'required|digits:10|unique:customers,mobile_no',
            'password' => 'required|string',

        ]);

        if ($validator->passes()) {

            $ins['first_name'] = $request->first_name;
            $ins['last_name'] = $request->last_name;
            $ins['email'] = $request->email;
            $ins['mobile_no'] = $request->mobile_no;
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

        $checkCustomer = Customer::where('email', $email)->where('password', Hash::make($password))->first();
        if( $checkCustomer ) {
            $error = 0;
            $message = 'Login Success';
            $status = 'success';
        } else {
            $error = 1;
            $message = 'Invalid credentials';
            $status = 'error';
        }
        return array( 'error' => $error, 'message' => $message, 'status' => $status );

    }
}
