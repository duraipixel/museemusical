<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\BrandsExport;
use App\Models\Master\Brands;
use App\Models\Master\State;
use Illuminate\Support\Facades\DB;
use DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Auth;
use Excel;
use PDF;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $title = "Brand";
        if ($request->ajax()) {
            $data =Brands::select('brands.*','users.name as users_name',DB::raw(" IF(brands.status = 2, 'Inactive', 'Active') as user_status"))->join('users', 'users.id', '=', 'brands.added_by');
            $status = $request->get('status');
            $keywords = $request->get('search')['value'];
            $datatables =  Datatables::of($data)
                ->filter(function ($query) use ($keywords, $status) {
                    if ($status) {
                        return $query->where('brands.status', 'like', "%{$status}%");
                    }
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        return $query->where('brands.brand_name', 'like', "%{$keywords}%")->orWhere('users.name', 'like', "%{$keywords}%")->orWhere('brands.short_description', 'like', "%{$keywords}%")->orWhere('brands.notes', 'like', "%{$keywords}%")->orWhere("brands.order_by",'like', "%{$keywords}%")->orWhereDate("brands.created_at", $date);
                    }
                })
                ->addIndexColumn()
               
                ->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        $status = '<a href="javascript:void(0);" class="badge badge-light-success" tooltip="Click to Inactive" onclick="return commonChangeStatus(' . $row->id . ', 2, \'brand\')">Active</a>';
                    } else {
                        $status = '<a href="javascript:void(0);" class="badge badge-light-danger" tooltip="Click to Active" onclick="return commonChangeStatus(' . $row->id . ', 1, \'brand\')">Inactive</a>';
                    }
                    return $status;
                })
                ->editColumn('brand_logo', function ($row) {
                    if ($row->brand_logo) {

                        $path = asset($row->brand_logo);
                        $brand_logo = '<div class="symbol symbol-45px me-5"><img src="' . $path . '" alt="" /><div>';
                    } else {
                        $path = asset('userImage/no_Image.jpg');
                        $brand_logo = '<div class="symbol symbol-45px me-5"><img src="' . $path . '" alt="" /><div>';
                    }
                    return $brand_logo;
                })
                ->editColumn('brand_banner', function ($row) {
                    if ($row->brand_banner) {

                        $path = asset($row->brand_banner);
                        $brand_banner = '<div class="symbol symbol-45px me-5"><img src="' . $path . '" alt="" /><div>';
                    } else {
                        $path = asset('userImage/no_Image.jpg');
                        $brand_banner = '<div class="symbol symbol-45px me-5"><img src="' . $path . '" alt="" /><div>';
                    }
                    return $brand_banner;
                })
                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })

                ->addColumn('action', function ($row) {
                    $edit_btn = '<a href="javascript:void(0);" onclick="return  openForm(\'brand\',' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                    <i class="fa fa-edit"></i>
                </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'brand\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';

                    return $edit_btn . $del_btn;
                })
                ->rawColumns(['action', 'status', 'brand_logo','brand_banner']);
            return $datatables->make(true);
        }
        return view('platform.master.brand.index');
    }
    public function modalAddEdit(Request $request)
    {
        $id                 = $request->id;
        $info               = '';
        $modal_title        = 'Add Brand';
        if (isset($id) && !empty($id)) {
            $info           = Brands::find($id);
            $modal_title    = 'Update Brand';
        }
        
        return view('platform.master.brand.add_edit_modal', compact('info', 'modal_title'));
    }
    public function saveForm(Request $request,$id = null)
    {
        // dd($request->all());
        $id             = $request->id;
        $validator      = Validator::make($request->all(), [
                                'brand_name' => 'required|string|unique:brands,brand_name,' . $id . ',id,deleted_at,NULL',
                                'avatar_logo' => 'mimes:jpeg,png,jpg',
                                'avatar_banner' => 'mimes:jpeg,png,jpg'
                            ]);

        if ($validator->passes()) {
            
            if ($request->file('avatar_logo')) {
                $filename       = time() . '_' . $request->avatar_logo->getClientOriginalName();
                $folder_name    = 'brand/' . $request->brand_name . '/brand_logo/';
                // dd($folder_name);
                $existID = '';
                if($id)
                {
                  
                    $existID = Brands::find($id);
                    $deleted_file = $existID->brand_logo;
                    if(File::exists($deleted_file)) {
                        File::delete($deleted_file);
                    }
                }
               
                $path           = $folder_name . $filename;
                $request->avatar_logo->move(public_path($folder_name), $filename);
                // dd($path);
                $ins['brand_logo']   = $path;
            }

            if ($request->file('avatar_banner')) {
                $filename       = time() . '_' . $request->avatar_banner->getClientOriginalName();
                $folder_name    = 'brand/' . $request->brand_name . '/brand_banner/';
                
                $existID = '';

                if($id)
                {
                    $existID = Brands::find($id);
                    $deleted_file = $existID->brand_banner;
                    if(File::exists($deleted_file)) {
                        File::delete($deleted_file);
                    }
                }
                
                $path           = $folder_name . $filename;
                $request->avatar_banner->move(public_path($folder_name), $filename);
                $ins['brand_banner']   = $path;
            }
            if ($request->image_remove_logo == "yes") {
                $ins['brand_logo'] = '';
            }
            if ($request->image_remove_banner == "yes") {
                $ins['brand_banner'] = '';
            }
           

            $ins['brand_name']                        = $request->brand_name;
            $ins['short_description']                   = $request->short_description;
            $ins['notes']                         = $request->notes;
            $ins['order_by']                         = $request->order_by;
            $ins['added_by']        = Auth::id();
            if($request->status == "1")
            {
                $ins['status']          = 1;
            }
            else{
                $ins['status']          = 2;
            }
            $error                  = 0;

            $info                   = Brands::updateOrCreate(['id' => $id], $ins);
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
        $info       = Brands::find($id);
        $info->delete();
        // echo 1;
        return response()->json(['message'=>"Successfully deleted state!",'status'=>1]);
    }
    public function changeStatus(Request $request)
    {
        $id             = $request->id;
        $status         = $request->status;
        $info           = Brands::find($id);
        $info->status   = $status;
        $info->update();
        // echo 1;
        return response()->json(['message'=>"You changed the state status!",'status'=>1]);

    }
    public function export()
    {
        return Excel::download(new BrandsExport, 'brand.xlsx');
    }

    public function exportPdf()
    {
        // $list       = OrderStatus::select('status_name', 'added_by', 'description', 'order', DB::raw(" IF(status = 2, 'Inactive', 'Active') as user_status"))->get();
        $list       = Brands::select('brands.*','users.name as users_name',DB::raw(" IF(brands.status = 2, 'Inactive', 'Active') as user_status"))->join('users', 'users.id', '=', 'brands.added_by')->get();
        $pdf        = PDF::loadView('platform.exports.brand.excel', array('list' => $list, 'from' => 'pdf'))->setPaper('a4', 'landscape');;
        return $pdf->download('brand.pdf');
    }
}
