<?php

namespace App\Http\Controllers;

use App\Mail\TestMail;
use App\Models\GlobalSettings;
use App\Models\Master\EmailTemplate;
use App\Models\Order;
use App\Models\SmsTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PDF;
use Mail;

class TestController extends Controller
{
    public function index(Request $request) {

        // $number = ['919551706025'];
        // $name   = 'Durairaj';
        // $orderId = 'IOP9090909P';
        // $companyName = 'Musee Musical';
        // $credentials = 'Password is 90909090';
        // $message = "Dear $name, Ref.Id $orderId, Thank you for register with $companyName. Your credentials are $credentials. -MUSEE MUSICAL";
        // sendSMS($number, $message);
        $this->sendSms('order_shipping');
        /***** All sms tempate working , checked with dynamci content from database is done and working **/
        
    }

    public function sendSms($sms_type)
    {
        $info = SmsTemplate::where('sms_type', $sms_type)->first();
        if( isset( $info ) && !empty( $info ) ) {

            $number = ['919551706025'];
            $name   = 'Durairaj';
            $reference_id = 'ORD2015';
            $company_name = $info->company_name;
            $credential = 'email/password';
            $subscribtion_id = '#SUB2022';
            $rupees = 'RS250000';
            $payment_method = 'online razorpay';
            $first_name  = 'Durai';
            $last_name  = 'raj';
            $order_no = 'ORD2013';
            $company_url  = 'https://www.onlinemuseemusical.com/';
            $latest_update = 'Latest Updates';
            $tracking_no = '#um89898990000009';
            $tracking_url = 'https://www.onlinemuseemusical.com/';

            $message = $info->template_content;
            eval("\$message = \"$message\";");

            $params = array(
                'entityid' => $info->peid_no,
                'tempid' => $info->tdlt_no,
                'sid'   => urlencode(current(explode(",",$info->header)))
            );

            sendSMS($number, $message, $params);
        }
    }

    public function invoiceSample(Request $request)
    {
        $info = 'teste';
        
        // $pdf = PDF::loadView('platform.invoice.index', compact('info'));    
        // Storage::put('public/invoice_order/121220252.pdf', $pdf->output());
        $order_info = Order::find(5);
        $globalInfo = GlobalSettings::first();
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
}
