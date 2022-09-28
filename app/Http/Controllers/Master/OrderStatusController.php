<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\OrderStatus;
use App\Exports\OrderStatusExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Auth;
use Excel;
use PDF;

class OrderStatusController extends Controller
{
    public function index(Request $request)
    { $title = "Order Status";
        if ($request->ajax()) {
            $data = OrderStatus::select('order_statuses.*','users.name as users_name', DB::raw(" IF(order_statuses.status = 2, 'Inactive', 'Active') as user_status"))->join('users', 'users.id', '=', 'order_statuses.added_by');
            $status = $request->get('status');
            $keywords = $request->get('search')['value'];
            $datatables =  Datatables::of($data)
                ->filter(function ($query) use ($keywords, $status) {
                    if ($status) {
                        return $query->where('order_statuses.status', 'like', "%{$status}%");
                    }
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        return $query->where('order_statuses.status_name', 'like', "%{$keywords}%")->orWhere('users.name', 'like', "%{$keywords}%")->orWhere('order_statuses.description', 'like', "%{$keywords}%")->orWhere('order_statuses.order', 'like', "%{$keywords}%")->orWhereDate("order_statuses.created_at", $date);
                    }
                })
                ->addIndexColumn()
               
                ->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        $status = '<a href="javascript:void(0);" class="badge badge-light-success" tooltip="Click to Inactive" onclick="return commonChangeStatus(' . $row->id . ', 2, \'order-status\')">Active</a>';
                    } else {
                        $status = '<a href="javascript:void(0);" class="badge badge-light-danger" tooltip="Click to Active" onclick="return commonChangeStatus(' . $row->id . ', 1, \'order-status\')">Inactive</a>';
                    }
                    return $status;
                })

                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })

                ->addColumn('action', function ($row) {
                    $edit_btn = '<a href="javascript:void(0);" onclick="return  openForm(\'order-status\',' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                    <i class="fa fa-edit"></i>
                </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'order-status\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';

                    return $edit_btn . $del_btn;
                })
                ->rawColumns(['action', 'status', 'image']);
            return $datatables->make(true);
        }
       
        return view('platform.master.order-status.index');

    }
    public function modalAddEdit(Request $request)
    {
        $id                 = $request->id;
        $info               = '';
        $modal_title        = 'Add Order Status';
        if (isset($id) && !empty($id)) {
            $info           = OrderStatus::find($id);
            $modal_title    = 'Update User';
        }
        return view('platform.master.order-status.add_edit_modal', compact('info', 'modal_title'));
    }
    public function saveForm(Request $request,$id = null)
    {
        // dd($request->all());
        $id             = $request->id;
        $validator      = Validator::make($request->all(), [
                                'status_name' => 'required|string|unique:order_statuses,status_name,' . $id . ',id,deleted_at,NULL',
                            ]);

        if ($validator->passes()) {
           
            $ins['status_name']                 = $request->status_name;
            $ins['description']                 = $request->description;
            $ins['order']                       = $request->order;
            $ins['added_by']        = Auth::id();
            $ins['status']          = 1;
            $error                  = 0;

            $info                   = OrderStatus::updateOrCreate(['id' => $id], $ins);
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
        $info       = OrderStatus::find($id);
        $info->delete();
        // echo 1;
        return response()->json(['message'=>"Successfully deleted order status!",'status'=>1]);
    }
    public function changeStatus(Request $request)
    {
        $id             = $request->id;
        $status         = $request->status;
        $info           = OrderStatus::find($id);
        $info->status   = $status;
        $info->update();
        // echo 1;
        return response()->json(['message'=>"You changed the order status!",'status'=>1]);

    }
    public function export()
    {
        return Excel::download(new OrderStatusExport, 'order_status.xlsx');
    }

    public function exportPdf()
    {
        // $list       = OrderStatus::select('status_name', 'added_by', 'description', 'order', DB::raw(" IF(status = 2, 'Inactive', 'Active') as user_status"))->get();
        $list       = OrderStatus::select('order_statuses.status_name','order_statuses.description','order_statuses.created_at','order_statuses.order','users.name as users_name', DB::raw(" IF(order_statuses.status = 2, 'Inactive', 'Active') as user_status"))->join('users', 'users.id', '=', 'order_statuses.added_by')->get();
        $pdf        = PDF::loadView('platform.exports.order_status.excel', array('list' => $list, 'from' => 'pdf'))->setPaper('a4', 'landscape');;
        return $pdf->download('order_status.pdf');
    }
    
}
