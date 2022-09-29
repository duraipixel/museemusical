<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\PincodeExport;

use App\Models\Master\Pincode;
use Illuminate\Support\Facades\DB;
use DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Auth;
use Excel;
use PDF;
class PincodeController extends Controller
{
    public function index(Request $request)
    {
        $title = "Country";
        if ($request->ajax()) {
            $data = Pincode::select('pincodes.*','users.name as users_name',DB::raw(" IF(pincodes.status = 2, 'Inactive', 'Active') as user_status"))->join('users', 'users.id', '=', 'pincodes.added_by');
            $status = $request->get('status');
            $keywords = $request->get('search')['value'];
            $datatables =  Datatables::of($data)
                ->filter(function ($query) use ($keywords, $status) {
                    if ($status) {
                        return $query->where('pincodes.status', 'like', "%{$status}%");
                    }
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        return $query->where('pincodes.pincode', 'like', "%{$keywords}%")->orWhere('pincodes.description', 'like', "%{$keywords}%")->orWhereDate("pincodes.created_at", $date);
                    }
                })
                ->addIndexColumn()
               
                ->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        $status = '<a href="javascript:void(0);" class="badge badge-light-success" tooltip="Click to Inactive" onclick="return commonChangeStatus(' . $row->id . ', 2, \'pincode\')">Active</a>';
                    } else {
                        $status = '<a href="javascript:void(0);" class="badge badge-light-danger" tooltip="Click to Active" onclick="return commonChangeStatus(' . $row->id . ', 1, \'pincode\')">Inactive</a>';
                    }
                    return $status;
                })

                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })

                ->addColumn('action', function ($row) {
                    $edit_btn = '<a href="javascript:void(0);" onclick="return  openForm(\'pincode\',' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                    <i class="fa fa-edit"></i>
                </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'pincode\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';

                    return $edit_btn . $del_btn;
                })
                ->rawColumns(['action', 'status', 'image']);
            return $datatables->make(true);
        }
        return view('platform.master.pincode.index');
    }
    public function modalAddEdit(Request $request)
    {
        $id                 = $request->id;
        $info               = '';
        $modal_title        = 'Add Country';
        if (isset($id) && !empty($id)) {
            $info           = Pincode::find($id);
            $modal_title    = 'Update Country';
        }
        return view('platform.master.pincode.add_edit_modal', compact('info', 'modal_title'));
    }
    public function saveForm(Request $request,$id = null)
    {
        // dd($request->all());
        $id             = $request->id;
        $validator      = Validator::make($request->all(), [
                                'pincode' => 'required|string|unique:pincodes,pincode,' . $id . ',id,deleted_at,NULL',
                             
                            ]);

        if ($validator->passes()) {
           
            $ins['pincode']                        = $request->pincode;
            $ins['description']                   = $request->description;
            $ins['added_by']        = Auth::id();
            if($request->status == "1")
            {
                $ins['status']          = 1;
            }
            else{
                $ins['status']          = 0;
            }
            $error                  = 0;

            $info                   = Pincode::updateOrCreate(['id' => $id], $ins);
            $message                = (isset($id) && !empty($id)) ? 'Updated Successfully' : 'Added successfully';
        } 
        else {
            $error      = 1;
            $message    = $validator->errors()->all();
        }
        return response()->json(['error' => $error, 'message' => $message]);
    }
    public function delete(Request $request)
    {
        $id         = $request->id;
        $info       = Pincode::find($id);
        $info->delete();
        // echo 1;
        return response()->json(['message'=>"Successfully deleted pincode!",'status'=>1]);
    }
    public function changeStatus(Request $request)
    {
        $id             = $request->id;
        $status         = $request->status;
        $info           = Pincode::find($id);
        $info->status   = $status;
        $info->update();
        // echo 1;
        return response()->json(['message'=>"You changed the Pincode status!",'status'=>1]);

    }
    public function export()
    {
        return Excel::download(new PincodeExport, 'pincode.xlsx');
    }

    public function exportPdf()
    {
        // $list       = OrderStatus::select('status_name', 'added_by', 'description', 'order', DB::raw(" IF(status = 2, 'Inactive', 'Active') as user_status"))->get();
        $list       = Pincode::select('pincodes.*', 'users.name as users_name',DB::raw(" IF(pincodes.status = 2, 'Inactive', 'Active') as user_status"))->join('users', 'users.id', '=', 'pincodes.added_by')->get();
        $pdf        = PDF::loadView('platform.exports.pincode.excel', array('list' => $list, 'from' => 'pdf'))->setPaper('a4', 'landscape');;
        return $pdf->download('pincode.pdf');
    }
}
