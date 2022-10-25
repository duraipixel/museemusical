<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\SubCategoryExport;
use App\Models\Category\MainCategory;
use App\Models\Category\SubCategory;
use Illuminate\Support\Facades\DB;
use DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Psy\Util\Str;
use Auth;
use Excel;
use PDF;

class SubCategoryController extends Controller
{
    public function index(Request $request)
    { 
        $title = "Sub Category";
        $category    = MainCategory::where('status','!=',0)->get();
        if ($request->ajax()) {
            $data       = SubCategory::select('sub_categories.*','main_categories.category_name as category_name','users.name as users_name')->join('main_categories', 'sub_categories.parent_id', '=', 'main_categories.id')->join('users', 'users.id', '=', 'sub_categories.added_by');
            $filter_category  ='';
            $status     = $request->get('status');
            $keywords   = $request->get('search')['value'];
            $filter_category   = $request->get('filter_category');
            $datatables =  Datatables::of($data)
                ->filter(function ($query) use ($keywords, $status,$filter_category) {
                    if ($status) {
                        return $query->where('sub_categories.status', 'like', "%{$status}%");
                    }
                    if ($filter_category) {
                        return $query->where('main_categories.category_name', 'like', "%{$filter_category}%")->orWhere('sub_categories.status', 'like', "%{$status}%");
                    }
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        return $query->where('sub_categories.name', 'like', "%{$keywords}%")->orWhere('users.name', 'like', "%{$keywords}%")->orWhere('main_categories.category_name', 'like', "%{$keywords}%")->orWhere('sub_categories.slug', 'like', "%{$keywords}%")->orWhereDate("sub_categories.created_at", $date);
                    }
                  
                })
                ->addIndexColumn()

                ->editColumn('image', function ($row) {
                    if ($row->image) {

                        $path   = asset($row->image);
                        $image  = '<div class="symbol symbol-45px me-5"><img src="' . $path . '" alt="" /><div>';
                    } else {
                        $path   = asset('userImage/no_Image.jpg');
                        $image  = '<div class="symbol symbol-45px me-5"><img src="' . $path . '" alt="" /><div>';
                    }
                    return $image;
                })
                ->addColumn('status', function ($row) {
                    $status = '<a href="javascript:void(0);" class="badge badge-light-'.(($row->status == 'published') ? 'success': 'danger').'" tooltip="Click to '.(($row->status == 'published') ? 'Unpublish' : 'Publish').'" onclick="return commonChangeStatus(' . $row->id . ',\''.(($row->status == 'published') ? 'unpublished': 'published').'\', \'sub_category\')">'.ucfirst($row->status).'</a>';
                    return $status;
                })
                

                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })

                ->addColumn('action', function ($row) {
                    $edit_btn   = '<a href="javascript:void(0);" onclick="return  openForm(\'sub_category\',' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                    <i class="fa fa-edit"></i>
                </a>';
                    $del_btn    = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'sub_category\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';

                    return $edit_btn . $del_btn;
                })
                ->rawColumns(['action', 'status', 'image']);
            return $datatables->make(true);
        }
        $breadCrum  = array('Masters', 'Dynamic Sub Category');
        $title      = 'Sub Category';
        return view('platform.category.sub_category.index',compact('category', 'breadCrum', 'title'));

    }
    public function modalAddEdit(Request $request)
    {
        $id                 = $request->id;
        $info               = '';
        $modal_title        = 'Add Sub Category';
        $category           = MainCategory::where('status',1)->get();
        if (isset($id) && !empty($id)) {
            $info           = SubCategory::find($id);
            $modal_title    = 'Update Sub Category';
        }
        
        return view('platform.category.sub_category.add_edit_modal', compact('info', 'modal_title','category'));
    }
    public function saveForm(Request $request,$id = null)
    {
        $id             = $request->id;
        $validator      = Validator::make($request->all(), [
                                'name' => 'required|string|unique:sub_categories,name,' . $id . ',id,deleted_at,NULL',
                                'category_name' => 'required',
                                'avatar' => 'mimes:jpeg,png,jpg',
                            ]);

        if ($validator->passes()) {
            
            if ($request->file('avatar')) {
                $filename       = time() . '_' . $request->avatar->getClientOriginalName();
                $folder_name    = 'categories/sub_category/' . str_replace(' ', '', $request->name) .'/';
                $existID        = '';
                $filename       = str_replace(' ', '', $filename);

                if($id)
                {
                    $existID        = SubCategory::find($id);
                    $deleted_file   = $existID->image;
                    if(File::exists($deleted_file)) {
                        File::delete($deleted_file);
                    }
                }
                $path           = $folder_name . $filename;
                $request->avatar->move(public_path($folder_name), $filename);
                $ins['image']   = $path;
            }
           
            if ($request->image_remove_image == "yes") {
                $ins['image'] = '';
            }
           
            $ins['parent_id']                   = $request->category_name;
            $ins['name']                        = $request->name;
            $ins['slug']                        = \Str::slug($request->name);
            $ins['description']                 = $request->description;
            $ins['tagline']                   = $request->tagline;
            $ins['order_by']                    = $request->order_by;
            $ins['added_by']                    = Auth::id();
            if($request->status == "1")
            {
                $ins['status']                  = 1;
            }
            else{
                $ins['status']                  = 2;
            }
            $error                              = 0;

            $info                               = SubCategory::updateOrCreate(['id' => $id], $ins);
            $message                            = (isset($id) && !empty($id)) ? 'Updated Successfully' : 'Added successfully';
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
        $info       = SubCategory::find($id);
        $info->delete();
        return response()->json(['message'=>"Successfully deleted Sub Category!",'status'=>1]);
    }
    public function changeStatus(Request $request)
    {
        $id             = $request->id;
        $status         = $request->status;
        $info           = SubCategory::find($id);
        $info->status   = $status;
        $info->update();
        // echo 1;
        return response()->json(['message'=>"You changed the Sub Category status!",'status'=>1]);

    }
    public function export()
    {
        return Excel::download(new SubCategoryExport, 'subCategory.xlsx');
    }

    public function exportPdf()
    {
        $list       = SubCategory::select('sub_categories.*','main_categories.category_name as category_name','users.name as users_name')->join('main_categories', 'sub_categories.parent_id', '=', 'main_categories.id')->join('users', 'users.id', '=', 'sub_categories.added_by')->get();
        $pdf        = PDF::loadView('platform.exports.sub_category.excel', array('list' => $list, 'from' => 'pdf'))->setPaper('a4', 'landscape');;
        return $pdf->download('subCategory.pdf');
    }
}
