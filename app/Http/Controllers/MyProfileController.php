<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
class MyProfileController extends Controller
{
    public function index(Request $request)
    {
       
        return view('platform.my-profile.index');
    }

    public function getTab(Request $request )
    {
        // dd($request->all());
        $id     = Auth::id();
        $data   = User::find($id);
        if($request['tabType'] == "password")
        {
            return view( 'platform.my-profile._change_password',compact('data'));

        }
        else{
            return view( 'platform.my-profile._profile_form',compact('data'));

        }
    }

    public function saveForm(Request $request)
    {
        $id             = $request->id;
        if($request['tab-name'] == "myaccount")
        {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email,' . $id . ',id,deleted_at,NULL',
                'mobile_number' => 'required|numeric|digits:10|unique:users,mobile_no,'. $id . ',id,deleted_at,NULL'
               
            ]);

            if ($validator->passes()) {
            
                $ins['name']            = $request->name;
                $ins['mobile_no']       = $request->mobile_number;
                $ins['address']         = $request->address;
    
                if ($request->file('avatar')) {
                    $filename       = time() . '_' . $request->avatar->getClientOriginalName();
                    $folder_name    = 'user/' . $request->email . '/profile/';
                    
                    $existID = '';
                    $existID = User::find($id);
                    $deleted_file = $existID->image;
                    if($id)
                    {
                        $existID = User::find($id);
                        $deleted_file = $existID->image;
                        if(File::exists($deleted_file)) {
                            File::delete($deleted_file);
                        }
                    }
                    if (!file_exists($folder_name)) {
                        mkdir($folder_name, 666, true);
                    }
                    if(File::exists($deleted_file)) {
                        File::delete($deleted_file);
                    }
                    $path           = $folder_name . $filename;
                    $request->avatar->move(public_path($folder_name), $filename);
                    $ins['image']   = $path;
                }
                if ($request->image_remove_image == "yes") {
                    $ins['image'] = '';
                }
    
    
                $error = 0;
                $info = User::updateOrCreate(['id' => $id],$ins);
                $message = (isset($id) && !empty($id)) ? 'Updated Successfully' :'Added successfully';
            } else {
                $error = 1;
                $message = $validator->errors()->all();
            }
            
    
        }
        else{
            
            $validator = Validator::make($request->all(), [
                'old_password' => 'required',
                'password' => 'required|min:6|required_with:password_confirmation|same:password_confirmation',
                'password_confirmation' => 'min:6'
               
            ]);
            if ($validator->passes()) {
                // dd((Hash::check($request->get('password'), Auth::user()->password)));
                if ((Hash::check($request->get('old_password'), Auth::user()->password))) {

                    $ins['password']            = Hash::make($request->password);
                    $error = 0;
                    $info = User::updateOrCreate(['id' => $id],$ins);
                    $message = (isset($id) && !empty($id)) ? 'Updated Successfully' :'Added successfully';

                }
                else{
                    $error = 1;
                    $message = "Old password dons't match";
                    return response()->json(['error'=> $error, 'message' => $message]);
                }

            }
            else {
                $error = 1;
                $message = $validator->errors()->all();
                return response()->json(['error'=> $error, 'message' => $message]);
            }

          
        }
      
        return response()->json(['error'=> $error, 'message' => $message]);
    }
    public function saveFormPassword(Request $request)
    {
        dd($request->all());
    }
}
