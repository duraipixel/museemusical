<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\DynamicMail;
use App\Models\Cart;
use App\Models\CartAddress;
use App\Models\Category\MainCategory;
use App\Models\GlobalSettings;
use App\Models\Master\Customer;
use App\Models\Master\CustomerAddress;
use App\Models\Master\EmailTemplate;
use Illuminate\Support\Facades\File;
use App\Models\Master\State;
use App\Services\ShipRocketService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Mail;

class CustomerController extends Controller
{

    public function verifyAccount(Request $request)
    {

        $email = $request->token;
        $email = base64_decode($email);
        $error = 1;
        $message = 'Token Expired';
        $customer = Customer::with('customerAddress')->where('email', $email)->whereNull('deleted_at')->first();
        if ($customer) {
            if (!empty($customer->verification_token)) {
                $customer->email_verified_at = Carbon::now();
                $customer->verification_token = null;
                $customer->update();
                $error = 0;
                $message = 'Account Verified Succesfull';
            }
        }

        return array('error' => $error, 'message' => $message, 'customer' => $customer);
    }

    public function registerCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string',
            'email' => 'required|email|unique:customers,email,id,deleted_at,NULL',
            'password' => 'required|string',

        ], ['email.unique' => 'Email id is already registered.Please try to login']);

        $customer = Customer::where('email', $request->email)->whereNull('deleted_at')->first();
        if (!$customer) {

            $ins['first_name'] = $request->firstName;
            $ins['email'] = $request->email;
            $ins['mobile_no'] = $request->mobile_no ?? null;
            $ins['customer_no'] = getCustomerNo();
            $ins['password'] = Hash::make($request->password);
            $ins['status'] = 'published';

            $customer_data = Customer::create($ins);

            $token_id = base64_encode($request->email);

            /** send email for new customer */
            $emailTemplate = EmailTemplate::select('email_templates.*')
                ->join('sub_categories', 'sub_categories.id', '=', 'email_templates.type_id')
                ->where('sub_categories.slug', 'new-registration')->first();

            $globalInfo = GlobalSettings::first();

            // $link = 'http://192.168.0.35:3000/#/verify-account/' . $token_id;
            $link = 'https://museemusical.shop/verify-account/' . $token_id;

            $customer_data->verification_token = $token_id;
            $customer_data->update();

            $extract = array(
                'name' => $request->firstName,
                'regards' => $globalInfo->site_name,
                'link' => '<a href="' . $link . '"> Verify Account </a>',
                'company_website' => '',
                'company_mobile_no' => $globalInfo->site_mobile_no,
                'company_address' => $globalInfo->address
            );

            $templateMessage = $emailTemplate->message;
            $templateMessage = str_replace("{", "", addslashes($templateMessage));
            $templateMessage = str_replace("}", "", $templateMessage);
            extract($extract);
            eval("\$templateMessage = \"$templateMessage\";");

            $send_mail = new DynamicMail($templateMessage, $emailTemplate->title);
            // return $send_mail->render();
            Mail::to($request->email)->send($send_mail);

            /** send sms for new customer */
            if ($request->mobile_no) {

                $sms_params = array(
                    'name' => $request->firstName,
                    'reference_id' => $ins['customer_no'],
                    'company_name' => env('APP_NAME'),
                    'login_details' => $ins['email'] . '/' . $request->password,
                    'mobile_no' => [$request->mobile_no]
                );

                sendMuseeSms('register', $sms_params);
            }

            $error = 0;
            $message = 'Verification email is sent to your email address, Please verify account to login';
            $status = 'success';
        } else {
            $error = 1;
            // $message = $validator->errors()->all();
            $message = ['Email id is already exists'];
            $status = 'error';
        }
        return array('error' => $error, 'message' => $message, 'status' => $status, 'customer_data' => $customer_data ?? '');
    }

    public function doLogin(Request $request)
    {
        $email = $request->email;
        $password = $request->password;
        $guest_token = $request->guest_token;
        $total_cart_count = 0;
        $checkCustomer = Customer::with(['customerAddress', 'customerAddress.subCategory'])->where('email', $email)->first();

        if ($checkCustomer) {
            if (Hash::check($password, $checkCustomer->password)) {
                if ($checkCustomer->email_verified_at == null) {
                    $error = 1;
                    $message = 'Verification pending check your mail';
                    $status = 'error';
                    $customer_data = '';
                    $customer_address = [];
                } else {

                    $error = 0;
                    $message = 'Login Success';
                    $status = 'success';
                    $customer_data = $checkCustomer;
                    $customer_address = $checkCustomer->customerAddress ?? [];

                    if ($guest_token) {

                        $cartData = Cart::where('token', $guest_token)->get();
                        if (isset($cartData) && count($cartData) > 0) {
                            Cart::where('token', $guest_token)->update(['token' => null, 'customer_id' => $checkCustomer->id]);
                        }
                    }
                    $cart_count = Cart::where('customer_id', $checkCustomer->id)->get();
                    $total_cart_count = count($cart_count);
                }
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

        return array('error' => $error, 'message' => $message, 'status' => $status, 'customer_data' => $customer_data, 'customer_addres' => $customer_address, 'total_cart_count' => $total_cart_count);
    }

    public function addCustomerAddress(Request $request, ShipRocketService $service)
    {

        if ($request->state_id) {
            $state_info = State::find($request->state_id);
            $ins['state'] = $state_info->state_name;
            $ins['stateid'] = $state_info->id;
        }
        // dd( $request->all() );
        // $details = $service->getShippingRocketOrderDimensions($request->customer_id);
        // echo 'duraira';die;
        $from_address_type = $request->from_address_type;
        $cart_info = Cart::where('customer_id', $request->customer_id)->first();

        $ins['customer_id'] = $request->customer_id;
        $ins['address_type_id'] = $request->address_type;
        $ins['name'] = $request->contact_name;
        $ins['email'] = $request->email;
        $ins['mobile_no'] = $request->mobile_no;
        $ins['address_line1'] = $request->address;
        $ins['country'] = 'india';
        $ins['post_code'] = $request->post_code;
        $ins['city'] = $request->city;

        $address_info = CustomerAddress::create($ins);

        $address = CustomerAddress::where('customer_id', $request->customer_id)->get();
        if (isset($cart_info) && !empty($cart_info)) {
            CartAddress::where('customer_id', $request->customer_id)
                ->where('address_type', $from_address_type)->delete();
            $ins_cart = [];
            $ins_cart['cart_token'] = $cart_info->guest_token;
            $ins_cart['customer_id'] = $request->customer_id;
            $ins_cart['address_type'] = $from_address_type;
            $ins_cart['name'] = $request->contact_name;
            $ins_cart['email'] = $request->email;
            $ins_cart['mobile_no'] = $request->mobile_no;
            $ins_cart['address_line1'] = $request->address;
            $ins_cart['country'] = 'india';
            $ins_cart['post_code'] = $request->post_code;
            $ins_cart['state'] = $ins['state'];
            $ins_cart['city'] = $request->city;

            CartAddress::create($ins_cart);
        }
        $shipRocketDetails = [];
        if ($from_address_type == 'shipping') {
            // $details = $service->getShippingRocketOrderDimensions($request->customer_id);
        }
        return array('error' => 0, 'shipRocketDetails' => $shipRocketDetails, 'message' => 'Address added successfully', 'status' => 'success', 'customer_address' => $address, 'address_info' => $address_info);
    }

    public function updateProfile(Request $request)
    {

        $customer_id = $request->customer_id;
        $first_name = $request->firstName;
        $last_name = $request->lastName;
        $email = $request->email;
        $mobile_no = $request->mobileNo;

        $customerInfo = Customer::find($customer_id);
        $customerInfo->first_name = $first_name;
        $customerInfo->last_name = $last_name;
        $customerInfo->email = $email;
        $customerInfo->mobile_no = $mobile_no;
        $customerInfo->update();
        return array('error' => 0, 'message' => 'Profile updated successfully', 'status' => 'success',  'customer_data' => $customerInfo);
    }
    public function updateProfileImage(Request $request)
    {
        $customerId = $request->customer_id;
        $customerInfo = Customer::find($customerId);
        $request->profile_image;
        if ($request->hasFile('profile_image')) {
            $filename       = time() . '_' . $request->profile_image->getClientOriginalName();
            $directory      = 'customer/' . $customerId;
            $filename       = $directory . '/' . $filename;
            Storage::deleteDirectory('public/' . $directory);
            Storage::disk('public')->put($filename, File::get($request->profile_image));

            $customerInfo->profile_image = $filename;
            $customerInfo->save();
        }
        return array('error' => 0, 'message' => 'Profile updated successfully', 'status' => 'success',  'customer_id' => $customerId);
    }

    public function changePassword(Request $request)
    {

        $customer_id = $request->customer_id;
        $current_password = $request->currentPassword;
        $newPassword = $request->password;

        $customerInfo = Customer::find($customer_id);
        if ($current_password == $newPassword) {
            $error = 1;
            $message = 'New password cannot be same as current password';
        } else if (isset($customerInfo) && !empty($customerInfo)) {

            if (Hash::check($current_password, $customerInfo->password)) {
                $error = 0;

                $customerInfo->password = Hash::make($newPassword);
                $customerInfo->update();

                $message = 'Password changed successfully';
            } else {
                $error = 1;
                $message = 'Current password is not match';
            }
        }

        return array('error' => $error, 'message' => $message);
    }

    public function deleteCustomerAddress(Request $request)
    {

        $address_id = $request->address_id;
        $addressInfo = CustomerAddress::find($address_id);
        $addressInfo->delete();
        $address = CustomerAddress::where('customer_id', $request->customer_id)->get();
        return array('error' => 0, 'message' => 'Address deleted successfully', 'status' => 'success', 'customer_address' => $address);
    }

    public function updateCustomerAddress(Request $request)
    {
        $address_id = $request->address_id;
        if ($request->state_id) {
            $state_info = State::find($request->state_id);
            $ins['state'] = $state_info->state_name;
            $ins['stateid'] = $state_info->id;
        }

        $ins['customer_id'] = $request->customer_id;
        $ins['address_type_id'] = $request->address_type_id;
        $ins['name'] = $request->name;
        $ins['email'] = $request->email;
        $ins['mobile_no'] = $request->mobile_no;
        $ins['address_line1'] = $request->address_line;
        $ins['country'] = 'india';
        $ins['post_code'] = $request->post_code;

        $ins['city'] = $request->city;

        CustomerAddress::updateOrCreate(['id' => $address_id], $ins);

        $address = CustomerAddress::where('customer_id', $request->customer_id)->get();
        return array('error' => 0, 'message' => 'Address added successfully', 'status' => 'success', 'customer_address' => $address);
    }

    public function getCustomerAddress(Request $request)
    {
        $address_id = $request->address_id;
        $res = [];
        if (isset($address_id) && !empty($address_id)) {
            $addressInfo = CustomerAddress::find($address_id);
            $res['address_id'] = $addressInfo->id;
            $res['address_line'] = $addressInfo->address_line1 ?? '';
            $res['address_type_id'] = (string)$addressInfo->address_type_id;
            $res['address_type_name'] = $addressInfo->subCategory->name ?? '';
            $res['city'] = $addressInfo->city ?? '';
            $res['customer_id'] = $addressInfo->customer_id;
            $res['email'] = $addressInfo->email;
            $res['mobile_no'] = $addressInfo->mobile_no;
            $res['name'] = $addressInfo->name;
            $res['post_code'] = $addressInfo->post_code ?? '';
            $res['state'] = $addressInfo->state ?? '';
            $res['stateid'] = $addressInfo->stateid ?? '';
        }

        $address_type       = MainCategory::where('slug', 'address-type')->first();
        $res['address_type'] = $address_type->subCategory ?? [];

        return $res;
    }

    public function sendPasswordLink(Request $request)
    {
        $email = $request->email;
        $token_id = base64_encode($email);

        $customer_info = Customer::where('email', $email)->first();

        if (isset($customer_info) && !empty($customer_info)) {
            $error = 0;
            $message = '';
            $customer_info->forgot_token = $token_id;
            $customer_info->update();
            /** send email for new customer */
            $emailTemplate = EmailTemplate::select('email_templates.*')
                ->join('sub_categories', 'sub_categories.id', '=', 'email_templates.type_id')
                ->where('sub_categories.slug', 'forgot-password')->first();

            $globalInfo = GlobalSettings::first();
            // $link = 'http://192.168.0.35:3000/#/reset-password/' . $token_id;
            //$link = 'https://museemusical.shop/#/reset-password/' . $token_id;
            $link = 'https://museemusical.shop/reset-password/' . $token_id;
            $extract = array(
                'name' => $customer_info->firstName . ' ' . $customer_info->last_name,
                'link' => '<a href="' . $link . '"> Reset Password </a>',
                'regards' => $globalInfo->site_name,
                'company_website' => '',
                'company_mobile_no' => $globalInfo->site_mobile_no,
                'company_address' => $globalInfo->address
            );

            $templateMessage = $emailTemplate->message;
            $templateMessage = str_replace("{", "", addslashes($templateMessage));
            $templateMessage = str_replace("}", "", $templateMessage);
            extract($extract);
            eval("\$templateMessage = \"$templateMessage\";");

            $send_mail = new DynamicMail($templateMessage, $emailTemplate->title);
            // return $send_mail->render();
            Mail::to($request->email)->send($send_mail);
        } else {
            $error = 1;
            $message = 'Email id is not exists';
        }
        return array('error' => $error, 'message' => $message);
    }

    public function resetPasswordLink(Request $request)
    {
        $customer_id = $request->customer_id;
        $password = $request->password;

        $customerInfo = Customer::find($customer_id);

        if (isset($customerInfo) && !empty($customerInfo)) {

            $customerInfo->password = Hash::make($password);
            $customerInfo->forgot_token = null;
            $customerInfo->update();

            $error = 0;
            $message = 'Password has been reset successfully. Please try login';
        } else {
            $error = 1;
            $message = 'Customer not found, Please try register';
        }
        return array('error' => $error, 'message' => $message);
    }

    public function checkValidToken(Request $request)
    {
        $token_id = $request->token_id;
        $customerInfo = Customer::where('forgot_token', $token_id)->first();

        if (isset($customerInfo) && !empty($customerInfo)) {
            $error = 0;
            $message = 'Token is valid';
            $data = [$customerInfo];
        } else {
            $error = 1;
            $message = 'Token is invalid';
            $data = [];
        }
        return array('error' => $error, 'message' => $message, 'data' => $data);
    }
}
