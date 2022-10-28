<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\TestimonialsExport;
use App\Models\Category\MainCategory;
use App\Models\Master\Brands;
use App\Models\Product\ProductCategory;
use App\Models\Testimonials;
use Illuminate\Support\Facades\DB;
use DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Auth;
use Excel;
use PDF;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $title                  = "Product";
        $breadCrum              = array('Products', 'Product');
        return view('platform.product.index', compact('title', 'breadCrum'));
    }

    public function addEditPage(Request $request)
    {
        $title                  = "Add Product";
        $breadCrum              = array('Products', 'Add Product');
        $productCategory        = ProductCategory::where('status', 'published')->get();

        $productLabels          = MainCategory::where(['slug' => 'product-labels', 'status' => 'published'])->first();
        $productTags            = MainCategory::where(['slug' => 'product-tags', 'status' => 'published'])->first();
        $brands                 = Brands::where('status', 'published')->get();
        
        return view('platform.product.form.add_edit_form', compact('title', 'breadCrum', 'productCategory', 'productLabels', 'productTags', 'brands' ));
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
