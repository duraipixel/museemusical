<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product\ProductCategory;
use DataTables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Auth;
use Excel;
use PDF;
class ProductCategoryController extends Controller
{
    public function index(Request $request)
    {
        $title                  = "Product Category";
        $breadCrum              = array('Products', 'Product Categories');

        if ($request->ajax()) {
            $data =ProductCategory::select('product_categories.*','users.name as users_name')->join('users', 'users.id', '=', 'product_categories.added_by');
            $status = $request->get('status');
            $keywords = $request->get('search')['value'];
            $datatables =  Datatables::of($data)
                ->filter(function ($query) use ($keywords, $status) {
                    if ($status) {
                        return $query->where('product_categories.status', 'like', "%{$status}%");
                    }
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        return $query->where('product_categories.name', 'like', "%{$keywords}%")->orWhere('users.name', 'like', "%{$keywords}%")->orWhere('product_categories.description', 'like', "%{$keywords}%")->orWhereDate("product_categories.created_at", $date);
                    }
                })
                ->addIndexColumn()
               
                ->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        $status = '<a href="javascript:void(0);" class="badge badge-light-success" tooltip="Click to Inactive" onclick="return commonChangeStatus(' . $row->id . ', 2, \'walk_throughs\')">Active</a>';
                    } else {
                        $status = '<a href="javascript:void(0);" class="badge badge-light-danger" tooltip="Click to Active" onclick="return commonChangeStatus(' . $row->id . ', 1, \'walk_throughs\')">Inactive</a>';
                    }
                    return $status;
                })
                
                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })

                ->addColumn('action', function ($row) {
                    $edit_btn = '<a href="javascript:void(0);" onclick="return  openForm(\'product-category\',' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                    <i class="fa fa-edit"></i>
                </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'product-category\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';

                    return $edit_btn . $del_btn;
                })
                ->rawColumns(['action', 'status', 'image']);
            return $datatables->make(true);
        }
        return view('platform.product_category.index', compact('title','breadCrum'));
    }

    public function modalAddEdit(Request $request)
    {
        // dd("1");
        $title                  = "Add Product Categories";
        $breadCrum              = array('Products', 'Add Product Categories');



        $id                 = $request->id;
        $info               = '';
        $modal_title        = 'Add Product Category';
        if (isset($id) && !empty($id)) {
            $info           = ProductCategory::find($id);
            $modal_title    = 'Update Product Category';
        }

        return view('platform.product_category.form.add_edit_form', compact('modal_title', 'breadCrum'));
    }
    public function saveForm(Request $request,$id = null)
    {
        $id             = $request->id;
        $validator      = Validator::make($request->all(), [
                                'title' => 'required|string|unique:testimonials,title,' . $id . ',id,deleted_at,NULL',
                                'avatar' => 'mimes:jpeg,png,jpg',
                                
                            ]);

        if ($validator->passes()) {
            
            if ($request->file('avatar')) {
                $filename       = time() . '_' . $request->avatar->getClientOriginalName();
                $folder_name    = 'testimonial/' . str_replace(' ', '', $request->title) . '/';
                // dd($folder_name);
                $existID = '';
                if($id)
                {
                  
                    $existID = Testimonials::find($id);
                    $deleted_file = $existID->image;
                    if(File::exists($deleted_file)) {
                        File::delete($deleted_file);
                    }
                }
               
                $path           = $folder_name . $filename;
                $request->avatar->move(public_path($folder_name), $filename);
                // dd($path);
                $ins['image']   = $path;
            }

            
            if ($request->image_remove_logo == "yes") {
                $ins['image'] = '';
            }
            

            $ins['title']                        = $request->title;
            $ins['short_description']                   = $request->short_description;
            $ins['long_description']                   = $request->long_description;
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

            $info                   = Testimonials::updateOrCreate(['id' => $id], $ins);
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
        $info       = Testimonials::find($id);
        $info->delete();
        // echo 1;
        return response()->json(['message'=>"Successfully deleted state!",'status'=>1]);
    }
    public function changeStatus(Request $request)
    {
        
        $id             = $request->id;
        $status         = $request->status;
        $info           = Testimonials::find($id);
        $info->status   = $status;
        $info->update();
        // echo 1;
        return response()->json(['message'=>"You changed the state status!",'status'=>1]);

    }
    public function export()
    {
        return Excel::download(new TestimonialsExport, 'testimonials.xlsx');
    }
    public function exportPdf()
    {
        $list       = Testimonials::select('testimonials.*','users.name as users_name',DB::raw(" IF(testimonials.status = 2, 'Inactive', 'Active') as user_status"))->join('users', 'users.id', '=', 'testimonials.added_by')->get();
        $pdf        = PDF::loadView('platform.exports.testimonials.excel', array('list' => $list, 'from' => 'pdf'))->setPaper('a4', 'landscape');;
        return $pdf->download('testimonial.pdf');
    }
}
