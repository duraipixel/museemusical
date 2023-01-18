<?php

namespace App\Http\Controllers;

use App\Exports\ChargesExport;
use App\Models\ShippingCharge;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Datatables;
use Yajra\DataTables\DataTables as DataTablesDataTables;
use Excel;
use PDF;

class ChargesController extends Controller
{
    public function index(Request $request)
    {

        $title                  = "Shipping Charges";
        $breadCrum              = array('Shipping Charges', 'Shipping Charges');
        if ($request->ajax()) {
            $data               = ShippingCharge::select('shipping_charges.*');
            $status             = $request->get('status');
            $keywords           = $request->get('search')['value'];
            $datatables         =  DataTablesDataTables::of($data)
                ->filter(function ($query) use ($keywords, $status) {
                    if ($status) {
                        return $query->where('shipping_charges.status', $status);
                    }
                    if ($keywords) {

                        if (!strpos($keywords, '.')) {
                            $date = date('Y-m-d', strtotime($keywords));
                        }
                        $query->where('shipping_charges.shipping_title', 'like', "%{$keywords}%")
                            ->orWhere('shipping_charges.minimum_order_amount', 'like', "%{$keywords}%")
                            ->orWhere('shipping_charges.charges', 'like', "%{$keywords}%");
                        if (isset($date)) {
                            $query->orWhereDate("shipping_charges.created_at", $date);
                        }

                        return $query;
                    }
                })
                ->addIndexColumn()

                ->editColumn('status', function ($row) {
                    $status = '<a href="javascript:void(0);" class="badge badge-light-' . (($row->status == 'published') ? 'success' : 'danger') . '" tooltip="Click to ' . (($row->status == 'published') ? 'Unpublish' : 'Publish') . '" onclick="return commonChangeStatus(' . $row->id . ', \'' . (($row->status == 'published') ? 'unpublished' : 'published') . '\', \'charges\')">' . ucfirst($row->status) . '</a>';
                    return $status;
                })


                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })

                ->addColumn('action', function ($row) {
                    $edit_btn = '<a href="javascript:void(0);" onclick="return  openForm(\'charges\',' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                    <i class="fa fa-edit"></i>
                </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'charges\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';

                    return $edit_btn . $del_btn;
                })
                ->rawColumns(['action', 'status']);
            return $datatables->make(true);
        }
    
        return view('platform.shipping_charges.index', compact('title', 'breadCrum'));
    }

    public function modalAddEdit(Request $request)
    {
        $title              = "Add Shipping Charges";
        $breadCrum          = array('Taxes & charges', 'Add Shipping Charges');

        $id                 = $request->id;
        $from               = $request->from;
        $info               = '';
        $modal_title        = 'Add Shipping Charges';
        if (isset($id) && !empty($id)) {
            $info           = ShippingCharge::find($id);
            $modal_title    = 'Update Shipping Charges';
        }
        return view('platform.shipping_charges.add_edit_modal', compact('modal_title', 'breadCrum', 'info', 'from'));
    }

    public function saveForm(Request $request, $id = null)
    {

        $id                         = $request->id;
        $validator                  = Validator::make($request->all(), [
            'title' => 'required|string|unique:taxes,title,' . $id . ',id,deleted_at,NULL',

        ]);

        if ($validator->passes()) {

            $ins['shipping_title']  = $request->title;
            $ins['minimum_order_amount'] = $request->minimum_order_amount;
            $ins['charges']         = $request->charges ?? 0;
            $ins['is_free']         = $request->is_free ? 'yes' : 'no';
            $ins['description']     = $request->description;

            if ($request->status == "1") {
                $ins['status']      = 'published';
            } else {
                $ins['status']      = 'unpublished';
            }
            $error                  = 0;

            $info                   = ShippingCharge::updateOrCreate(['id' => $id], $ins);
            $message                = (isset($id) && !empty($id)) ? 'Updated Successfully' : 'Added successfully';
        } else {
            $error      = 1;
            $message    = $validator->errors()->all();
        }
        return response()->json(['error' => $error, 'message' => $message]);
    }

    public function delete(Request $request)
    {
        $id         = $request->id;
        $info       = ShippingCharge::find($id);
        $info->delete();
        return response()->json(['message' => "Successfully deleted state!", 'status' => 1]);
    }

    public function changeStatus(Request $request)
    {

        $id             = $request->id;
        $status         = $request->status;
        $info           = ShippingCharge::find($id);
        $info->status   = $status;
        $info->update();
        return response()->json(['message' => "You changed the status!", 'status' => 1]);
    }
    public function export()
    {
        return Excel::download(new ChargesExport, 'charges.xlsx');
    }
    public function exportPdf()
    {
        $list       = ShippingCharge::select('shipping_charges.*')->get();
        $pdf        = PDF::loadView('platform.exports.charges.charges', array('list' => $list, 'from' => 'pdf'))->setPaper('a4', 'landscape');;
        return $pdf->download('charges.pdf');
    }
}
