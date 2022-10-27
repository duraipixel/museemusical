<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\CategoryMetaTags;
use Illuminate\Http\Request;
use App\Models\Product\ProductCategory;
use DataTables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Auth;
use Excel;
use Illuminate\Support\Facades\Storage;
use PDF;
class ProductCategoryController extends Controller
{
    public function index(Request $request)
    {
        $title                  = "Product Category";
        $breadCrum              = array('Products', 'Product Categories');

        if ($request->ajax()) {
            $data               = ProductCategory::select('product_categories.*','users.name as users_name', DB::raw('IF(mm_product_categories.parent_id = 0, "Parent", mm_parent_category.name ) as parent_name '))
                                                    ->join('users', 'users.id', '=', 'product_categories.added_by')
                                                    ->leftJoin('product_categories as parent_category', 'parent_category.id', '=', 'product_categories.id');
            $status             = $request->get('status');
            $keywords           = $request->get('search')['value'];
            $datatables         =  Datatables::of($data)
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
                    $status = '<a href="javascript:void(0);" class="badge badge-light-'.(($row->status == 'published') ? 'success': 'danger').'" tooltip="Click to '.(($row->status == 'published') ? 'Unpublish' : 'Publish').'" onclick="return commonChangeStatus(' . $row->id . ', \''.(($row->status == 'published') ? 'unpublished': 'published').'\', \'product-category\')">'.ucfirst($row->status).'</a>';
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
        
        $title              = "Add Product Categories";
        $breadCrum          = array('Products', 'Add Product Categories');

        $id                 = $request->id;
        $from               = $request->from;
        $info               = '';
        $modal_title        = 'Add Product Category';
        if (isset($id) && !empty($id)) {
            $info           = ProductCategory::find($id);
            $modal_title    = 'Update Product Category';
        }
        // dd( $info->meta);
        return view('platform.product_category.form.add_edit_form', compact('modal_title', 'breadCrum', 'info', 'from'));
    }
    public function saveForm(Request $request,$id = null)
    {
        
        $id             = $request->id;
        $validator      = Validator::make($request->all(), [
                            'category_name' => 'required|string|unique:product_categories,name,' . $id . ',id,deleted_at,NULL',
                            'avatar' => 'mimes:jpeg,png,jpg',
                        ]);

        if ($validator->passes()) {
            
            if ($request->image_remove_logo == "yes") {
                $ins['image'] = '';
            }
            if( !$request->is_parent ) {
                $ins['parent_id'] = $request->parent_category;
            }
            if( $request->is_tax ) {
                $ins['tax_id'] = $request->tax_id;
            }
            if( !$id ) {
                $ins['added_by'] = Auth::id();
            } else {
                $ins['updated_by'] = Auth::id();
            }

            $ins['name'] = $request->category_name;
            $ins['description'] = $request->description;
            $ins['order_by'] = $request->order_by;
            $ins['tag_line'] = $request->tag_line;

            if($request->status)
            {
                $ins['status']          = 'published';
            } else {
                $ins['status']          = 'unpublished';
            }
            $error                      = 0;
            $categeryInfo = ProductCategory::updateOrCreate(['id' => $id], $ins);
            $categoryId = $categeryInfo->id;

            if ($request->hasFile('categoryImage')) {
               
                $filename       = time() . '_' . $request->categoryImage->getClientOriginalName();
                $directory      = 'productCategory/'.$categoryId;
                $filename       = $directory.'/'.$filename.'/';
                Storage::deleteDirectory('public/'.$directory);
                Storage::disk('public')->put($filename, File::get($request->categoryImage));
                
                $categeryInfo->image = $filename;
                $categeryInfo->save();
            }

            $meta_title = $request->meta_title;
            $meta_keywords = $request->meta_keywords;
            $meta_description = $request->meta_description;

            if( !empty( $meta_title ) || !empty( $meta_keywords) || !empty( $meta_description ) ) {
                CategoryMetaTags::where('category_id',$categoryId)->delete();
                $metaIns['meta_title']          = $meta_title;
                $metaIns['meta_keyword']       = $meta_keywords;
                $metaIns['meta_description']    = $meta_description;
                $metaIns['category_id']         = $categoryId;
                CategoryMetaTags::create($metaIns);
            }
            $message                    = (isset($id) && !empty($id)) ? 'Updated Successfully' : 'Added successfully';
        } else {
            $error      = 1;
            $message    = $validator->errors()->all();
        }
        return response()->json(['error' => $error, 'message' => $message]);
    }
    public function delete(Request $request)
    {
        $id         = $request->id;
        $info       = ProductCategory::find($id);
        $info->delete();
        $directory      = 'productCategory/'.$id;
        Storage::deleteDirectory($directory);
        // echo 1;
        return response()->json(['message'=>"Successfully deleted state!",'status'=>1]);
    }
    public function changeStatus(Request $request)
    {
        
        $id             = $request->id;
        $status         = $request->status;
        $info           = ProductCategory::find($id);
        $info->status   = $status;
        $info->update();
        // echo 1;
        return response()->json(['message'=>"You changed the state status!",'status'=>1]);

    }
    public function export()
    {
        return Excel::download(new ProductCategoryExport, 'testimonials.xlsx');
    }
    public function exportPdf()
    {
        $list       = ProductCategory::select('testimonials.*','users.name as users_name',DB::raw(" IF(testimonials.status = 2, 'Inactive', 'Active') as user_status"))->join('users', 'users.id', '=', 'testimonials.added_by')->get();
        $pdf        = PDF::loadView('platform.exports.testimonials.excel', array('list' => $list, 'from' => 'pdf'))->setPaper('a4', 'landscape');;
        return $pdf->download('testimonial.pdf');
    }
}
