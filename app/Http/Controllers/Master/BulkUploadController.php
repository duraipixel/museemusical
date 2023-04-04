<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Imports\PincodeUpload;
use App\Imports\StateUpload;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BulkUploadController extends Controller
{
    public function index()
    {
        $title          = "Location Bulk Upload";
        $breadCrum      = array('Location', 'Location Bulk Upload');
        $params         = array(
            'title' => $title,
            'breadCrum' => $breadCrum,
        );    
        return view('platform.master.bulk-upload.index',$params);
    }
    public function doAttributesBulkUploadPincode(Request $request)
    {
        Excel::import(new PincodeUpload,request()->file('file'));
        return response()->json(['error'=> 0, 'message' => 'Imported successfully']);
    }
    public function doAttributesBulkUploadState(Request $request)
    {
        Excel::import(new StateUpload,request()->file('file'));
        return response()->json(['error'=> 0, 'message' => 'Imported successfully']);
    }
}
