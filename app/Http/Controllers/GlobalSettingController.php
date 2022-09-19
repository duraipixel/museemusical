<?php

namespace App\Http\Controllers;

use App\Models\GlobalSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GlobalSettingController extends Controller
{
    public function index(Request $request)
    {
        return view('platform.global.index');
    }

    public function saveForm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_name' => 'required|string|max:255',
            'site_mobile_no' => 'required|string|max:12',
            'site_email' => 'required|email',
            'website' => 'url'
        ]);

        if ($validator->passes()) {
            $id = '';
            $ins['site_name'] = $request->site_name;
            $ins['site_email'] = $request->site_email;
            $ins['site_mobile_no'] = $request->site_mobile_no;
            $ins['copyrights'] = $request->copyrights;
            $ins['website'] = $request->website;
            
            $error = 0;
            $info = GlobalSettings::updateOrCreate(['id' => $id],$ins);
            $message = (isset($id) && !empty($id)) ? 'Updated Successfully' :'Added successfully';
        } else {
            $error = 1;
            $message = $validator->errors()->all();
        }
        return response()->json(['error'=> $error, 'message' => $message]);
    }
}
