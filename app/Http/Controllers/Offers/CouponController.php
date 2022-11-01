<?php

namespace App\Http\Controllers\Offers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\CouponExport;
use App\Models\Offers\Coupons;
use App\Models\Offers\CouponProduct;
use App\Models\Offers\CouponCustomer;
use App\Models\Offers\CouponCategory;
use Illuminate\Support\Facades\DB;
use DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Auth;
use Psy\Util\Str;
use Excel;
use Illuminate\Support\Arr;
use PDF; 
class CouponController extends Controller
{
    public function index(Request $request)
    {
        $title = "Coupons";
        $breadCrum = array('Coupons');
        if ($request->ajax()) {
            $data               = Coupons::select('coupons.*');
            $status             = $request->get('status');
            $keywords           = $request->get('search')['value'];
            $datatables         =  Datatables::of($data)
                ->filter(function ($query) use ($keywords, $status) {
                    if ($status) {
                        return $query->where('coupon.status', 'like', "%{$status}%");
                    }
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        return $query->where('coupon.coupon_name', 'like', "%{$keywords}%")->orWhere('coupon.coupon_code', 'like', "%{$keywords}%")->orWhere('coupon.status', 'like', "%{$keywords}%")->orWhereDate("coupon.created_at", $date);
                    }
                })
                ->addIndexColumn()
               
                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })

                ->addColumn('action', function ($row) {
                    $edit_btn = '<a href="javascript:void(0);" onclick="return  openForm(\'coupon\',' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                    <i class="fa fa-edit"></i>
                </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'coupon\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';

                    return $edit_btn . $del_btn;
                })
                ->rawColumns(['action']);
            return $datatables->make(true);
        }
        return view('platform.offers.coupon.index', compact('breadCrum', 'title'));

    }
    public function modalAddEdit(Request $request)
    {
        $id                 = $request->id;
        $info               = '';
        $modal_title        = 'Add Coupon';
        if (isset($id) && !empty($id)) {
            $info           = Coupons::find($id);
            $modal_title    = 'Update Coupon';
        }
        
        return view('platform.offers.coupon.add_edit_modal', compact('info', 'modal_title'));
    }
    public function couponGendrate(Request $request)
    {
        $permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $val =  substr(str_shuffle($permitted_chars), 0, 6);
        return $val;
    }
    public function couponType(Request $request)
    {
        $name = $request->name;
        if($name == '1')
        {
            $data = DB::table('products')->select('id','product_name')->get();
            $title = "Product"; 
            foreach($data as $key=>$val)
            {
                $value[] = "<option value=".$val->id.">".$val->product_name."</option>";
            }
            return response()->json(["data"=>$value,"title"=>$title]);
        }
        if($name == '2')
        {
            $data = DB::table('customers')->select('id','first_name')->get();
            $title = "Customer"; 
            foreach($data as $key=>$val)
            {
                $value[] = "<option value=".$val->id.">".$val->first_name."</option>";
            }
            return response()->json(["data"=>$value,"title"=>$title]);
        }
        if($name == '3')
        {
            $data = DB::table('product_categories')->select('id','name')->get();
            $title = "Categories"; 
            foreach($data as $key=>$val)
            {
                $value[] = "<option value=".$val->id.">".$val->name."</option>";
            }
            return response()->json(["data"=>$value,"title"=>$title]);
        }

    }
    public function saveForm(Request $request,$id = null)
    {
        $id                         = $request->id;
        $validator                  = Validator::make($request->all(), [
                                        'calculate_type' => 'required',
                                        'calculate_value' => 'required',
                                        'coupon_type' => 'required',
                                        'coupon_name' => 'required|string|unique:coupons,coupon_name,' . $id . ',id,deleted_at,NULL',
                                        'coupon_code' => 'required|string|unique:coupons,coupon_code,' . $id . ',id,deleted_at,NULL',
                                        'start_date' => 'required',
                                        'end_date' => 'required',
                                        'repeated_coupon'=>'numeric|gt:0',
                                        'quantity'=>'numeric|gt:0',
                                        'minimum_order_value'=>'numeric|gt:0',
                                    ]);

        if ($validator->passes()) {
            $ins['coupon_name']                 = $request->coupon_name;
            $ins['coupon_code']                 = $request->coupon_code;
            $ins['coupon_sku']                  = \Str::slug($request->coupon_name);;
            $ins['start_date']                  = $request->start_date;
            $ins['end_date']                    = $request->end_date;
            $ins['calculate_type']              = $request->calculate_type;
            $ins['calculate_value']             = $request->calculate_value;
            $ins['coupon_type']                 = $request->coupon_type;
            $ins['minimum_order_value']         = $request->minimum_order_value;
            $ins['quantity']                    = $request->quantity;
            $ins['repeated_use_count']          = $request->repeated_coupon;
            $ins['order_by']                    = $request->order_by;
            $ins['added_by']            = Auth::id();

            if($request->status == "1")
            {
                $ins['status']          = 'published';
            } else {
                $ins['status']          = 'unpublished';
            }
            $error                  = 0;
           
            $info                   = Coupons::updateOrCreate(['id' => $id], $ins);
            $storeId = $info->id;
            if($request->coupon_type == "1" && !empty($storeId)){
                CouponProduct::where('coupon_id',$id)->forceDelete();
                foreach($request->product_id as $key=>$val)
                {
                    $data['coupon_id']          = $storeId;
                    $data['product_id']         = $val;
                    $data['quantity']           = $request->quantity;

                    $couponProduct = CouponProduct::Create($data);
                }


            }
            else if($request->coupon_type == "2" && !empty($storeId)){
                CouponCustomer::where('coupon_id',$id)->forceDelete();

                foreach($request->product_id as $key=>$val)
                {
                    $data['coupon_id']          = $storeId;
                    $data['customer_id']         = $val;
                    $data['quantity']           = $request->quantity;

                    $couponProduct = CouponCustomer::Create($data);
                }
            }
            else if($request->coupon_type == "3" && !empty($storeId)){
                CouponCategory::where('coupon_id',$id)->forceDelete();

                foreach($request->product_id as $key=>$val)
                {
                    $data['coupon_id']          = $storeId;
                    $data['category_id']         = $val;
                    $data['quantity']           = $request->quantity;

                    $couponProduct = CouponCategory::Create($data);
                }
            }
            
            $message   = (isset($id) && !empty($id)) ? 'Updated Successfully' : 'Added successfully';
        } 
        else {
            $error      = 1;
            $message    = $validator->errors()->all();
        }
        return response()->json(['error' => $error, 'message' => $message]);
    }
}
