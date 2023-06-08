<?php

namespace App\Http\Controllers;

use App\Mail\TestMail;
use App\Models\GlobalSettings;
use App\Models\Master\Brands;
use App\Models\Master\EmailTemplate;
use App\Models\Order;
use App\Models\Product\Product;
use App\Models\SmsTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use PDF;
use Mail;

class TestController extends Controller
{
    public function index(Request $request) {

        $number = ['919551706025'];
        $name   = 'Durairaj';
        $orderId = 'IOP9090909P';
        $companyName = 'Musee Musical';
        $credentials = 'durairamyb@mail.com/09876543456';
        $message = "Dear $name, Ref.Id $orderId, Thank you for register with $companyName. Your credentials are $credentials. -MUSEE MUSICAL";
        sendSMS($number, $message, []);
        
        // $response = Http::post('https://apiv2.shiprocket.in/v1/external/auth/login',[
        //     'header' => 'Content-Type: application/json',
        //     'email' => 'duraibytes@gmail.com',
        //     'password' => 'Pixel@2022'
        // ]);

        // dd( $response );
        
    }

    public function sendSms($sms_type, $details = [])
    {
        $info = SmsTemplate::where('sms_type', $sms_type)->first();
        if( isset( $info ) && !empty( $info ) ) {

            $number = ['919551706025'];
            $details = array(
                'name' => 'durairja',
                'reference_id' => '88978979',
                'company_name' => env('APP_NAME'),
                'login_details' => 'loginId:durairamyb@mail.com,password:09876543456',
                'mobile_no' => ['919551706025']
            );
            // $name   = 'Durairaj';
            // $reference_id = 'ORD2015';
            // $company_name = $info->company_name;
            // $credential = 'email/password';
            // $subscribtion_id = '#SUB2022';
            // $rupees = 'RS250000';
            // $payment_method = 'online razorpay';
            // $first_name  = 'Durai';
            // $last_name  = 'raj';
            // $order_no = 'ORD2013';
            // $company_url  = 'https://www.onlinemuseemusical.com/';
            // $latest_update = 'Latest Updates';
            // $tracking_no = '#um89898990000009';
            // $tracking_url = 'https://www.onlinemuseemusical.com/';

            $templateMessage = $info->template_content;
            $templateMessage = str_replace("{", "", addslashes($templateMessage));
            $templateMessage = str_replace("}", "", $templateMessage);
            
            extract($details);
            
            eval("\$templateMessage = \"$templateMessage\";");

            $params = array(
                'entityid' => $info->peid_no,
                'tempid' => $info->tdlt_no,
                'sid'   => urlencode(current(explode(",",$info->header)))
            );

            sendSMS($number, $templateMessage, $params);
        }
    }

    public function invoiceSample(Request $request)
    {
        $info = 'teste';
        
        // $order_info = Order::find(5);
        $order_info = Order::selectRaw('mm_orders.*, pay.order_id,pay.payment_no,pay.status as payment_status')->join(DB::raw('(select o.created_at,o.order_id, o.payment_no, o.status from mm_payments o WHERE o.created_at =( SELECT MAX(mm_payments.created_at) FROM mm_payments WHERE order_id = o.order_id ) ) as pay'), function ($join){
            $join->on(DB::raw('pay.order_id'), '=', 'orders.id');
        })->where('id', 77)->first();

        $globalInfo = GlobalSettings::first();
        // $pdf = PDF::loadView('platform.invoice.index', compact('order_info', 'globalInfo'));    
        // Storage::put('public/invoice_order/'.$order_info->order_no.'.pdf', $pdf->output());
        $pdf = PDF::loadView('platform.invoice.index', compact('order_info', 'globalInfo'))->setPaper('a4', 'portrait');
        return $pdf->stream('test.pdf');
    }

    public function sendMail()
    {
        // $email = 'duraibytes@gmail.com';
   
        // $mailData = [
        //     'title' => 'Demo Email',
        //     'url' => 'https://www.positronx.io'
        // ];
  
        // Mail::to($email)->send(new TestMail($mailData));
   
        // return response()->json([
        //     'message' => 'Email has been sent.'
        // ]);

        $emailTemplate = EmailTemplate::select('email_templates.*')
                                ->join('sub_categories', 'sub_categories.id', '=', 'email_templates.type_id')
                                ->where('sub_categories.slug', 'new-registration')->first();
        
        $globalInfo = GlobalSettings::first();
        

        $extract = array(
                        'name' => 'Durairaj', 
                        'regards' => $globalInfo->site_name, 
                        'company_website' => '',
                        'company_mobile_no' => $globalInfo->site_mobile_no,
                        'company_address' => $globalInfo->address 
                    );
        $templateMessage = $emailTemplate->message;
        $templateMessage = str_replace("{","",addslashes($templateMessage));
        $templateMessage = str_replace("}","",$templateMessage);
        extract($extract);
        eval("\$templateMessage = \"$templateMessage\";");

        $body = [
            'content' => $templateMessage,
            'title' => $emailTemplate->title
        ];
        $send_mail = new TestMail($templateMessage, $emailTemplate->title);
        // return $send_mail->render();
        Mail::to("durairaj.pixel@gmail.com")->send($send_mail);
        
    }

    public function generateSiteMap(Request $request)
    {
        $products = Product::all();
        $pages = array(
            'https://museemusical.shop',
            'https://museemusical.shop/#/privacypolicy',
            'https://museemusical.shop/#/TermsofUse',
            'https://museemusical.shop/#/returnpolicy',
            'https://museemusical.shop/#/shippingpolicy',
            'https://museemusical.shop/#/brand',
            
        );
        $brands = Brands::all();
        return response()->view('site-map', compact('products', 'brands', 'pages' ))->header('Content-Type', 'text/xml');
    }
}
