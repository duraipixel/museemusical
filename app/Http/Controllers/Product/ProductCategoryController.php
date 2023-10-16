<?php

namespace App\Http\Controllers\Product;

use App\Exports\ProductCategoryExport;
use App\Http\Controllers\Controller;
use App\Models\CategoryMetaTags;
use Illuminate\Http\Request;
use App\Models\Product\ProductCategory;
use App\Models\Settings\Tax;
use Illuminate\Validation\Rule;
use DataTables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Auth;
use Excel;
use Illuminate\Support\Facades\Storage;
use PDF;
use Image;
use Illuminate\Support\Str;

class ProductCategoryController extends Controller
{
    public function index(Request $request)
    {
        $title                  = "Product Category";
        $breadCrum              = array('Products', 'Product Categories');
        $taxData = Tax::where('status', 'published')->get();
        if ($request->ajax()) {
            $data               = ProductCategory::select('product_categories.*', 'users.name as users_name', 'taxes.title as tax', DB::raw('IF(mm_product_categories.parent_id = 0, "Parent", mm_parent_category.name ) as parent_name '))
                ->join('users', 'users.id', '=', 'product_categories.added_by')
                ->leftJoin('taxes', 'taxes.id', '=', 'product_categories.tax_id')
                ->leftJoin('product_categories as parent_category', 'parent_category.id', '=', 'product_categories.parent_id');
            $taxSearch          = '';
            $status             = $request->get('status');
            $taxSearch          = $request->get('filter_tax');
            $keywords           = $request->get('search')['value'];
            $datatables         =  Datatables::of($data)
                ->filter(function ($query) use ($keywords, $status, $taxSearch) {

                    return $query->when($status != '', function ($q) use ($status) {
                        $q->where('product_categories.status', '=', "$status");
                    })->when($taxSearch != '', function ($q) use ($taxSearch) {
                        $q->where('taxes.id', '=', "$taxSearch");
                    })->when($keywords != '', function ($q) use ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        $q->where('product_categories.name', 'like', "%{$keywords}%")
                            ->orWhere('users.name', 'like', "%{$keywords}%")
                            ->orWhere('taxes.title', 'like', "%{$keywords}%")
                            ->orWhere('product_categories.description', 'like', "%{$keywords}%")
                            ->orWhere('parent_category.name', 'like', "%{$keywords}%")
                            ->orWhereDate("product_categories.created_at", $date);
                    });
                })
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    $status = '<a href="javascript:void(0);" class="badge badge-light-' . (($row->status == 'published') ? 'success' : 'danger') . '" tooltip="Click to ' . (($row->status == 'published') ? 'Unpublish' : 'Publish') . '" onclick="return commonChangeStatus(' . $row->id . ', \'' . (($row->status == 'published') ? 'unpublished' : 'published') . '\', \'product-category\')">' . ucfirst($row->status) . '</a>';
                    return $status;
                })
                ->editColumn('tax', function ($row) {
                    return $row->tax ?? 'No';
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
        return view('platform.product_category.index', compact('title', 'breadCrum', 'taxData'));
    }

    public function modalAddEdit(Request $request)
    {

        $title              = "Add Product Categories";
        $breadCrum          = array('Products', 'Add Product Categories');

        $id                 = $request->id;
        $from               = $request->from;
        $info               = '';
        $modal_title        = 'Add Product Category';
        $taxAll             = Tax::where('status', 'published')->get();
        $productCategory    = ProductCategory::where('status', 'published')->where('parent_id', 0)->get();
        if (isset($id) && !empty($id)) {
            $info           = ProductCategory::find($id);
            $modal_title    = 'Update Product Category';
        }

        return view('platform.product_category.form.add_edit_form', compact('modal_title', 'breadCrum', 'info', 'from', 'productCategory', 'taxAll'));
    }

    public function saveForm(Request $request, $id = null)
    {

        $id             = $request->id;
        $parent_id      = $request->parent_category;
        $validator      = Validator::make($request->all(), [
            'name' => [
                'required', 'string',
                Rule::unique('product_categories')->where(function ($query) use ($id, $parent_id) {
                    return $query->where('parent_id', $parent_id)->where('deleted_at', NULL)->when($id != '', function ($q) use ($id) {
                        return $q->where('id', '!=', $id);
                    });
                }),
            ],

            'avatar' => 'mimes:jpeg,png,jpg',
            'tax_id' => 'required_if:is_tax,on'
        ]);

        $categoryId         = '';
        if ($validator->passes()) {

            if ($request->image_remove_logo == "yes") {
                $ins['image'] = '';
            }
            if (!$request->is_parent) {
                $ins['parent_id'] = $request->parent_category;
            } else {
                $ins['parent_id'] = 0;
            }
            if ($request->is_tax) {
                $ins['tax_id'] = $request->tax_id;
            } else {
                $ins['tax_id'] = null;
            }

            if ($request->is_home_menu) {
                $ins['is_home_menu'] = 'yes';
            } else {
                $ins['is_home_menu'] = 'no';
            }

            if (!$id) {
                $ins['added_by'] = Auth::id();
            } else {
                $ins['updated_by'] = Auth::id();
            }

            $ins['name'] = $request->name;
            $ins['description'] = $request->description;
            $ins['order_by'] = $request->order_by ?? 0;
            $ins['tag_line'] = $request->tag_line;

            if ($request->status) {
                $ins['status']          = 'published';
            } else {
                $ins['status']          = 'unpublished';
            }
            $parent_name = '';
            if (isset($parent_id) && !empty($parent_id)) {
                $parentInfo             = ProductCategory::find($parent_id);
                $parent_name            = $parentInfo->name;
            }

            $ins['slug']                = Str::slug($request->name);
            // dd( $ins );
            $error                      = 0;
            $categeryInfo               = ProductCategory::updateOrCreate(['id' => $id], $ins);
            $categoryId                 = $categeryInfo->id;
            $categeryInfo->order_by = $request->order_by ?? 0;

            if ($request->hasFile('categoryImage')) {

                $imagName               = time() . '_' . Str::replace(' ', "-", $request->categoryImage->getClientOriginalName());
                $directory              = 'productCategory/' . $categoryId . '/default';
                $filename               = $directory . '/' . $imagName . '/';
                Storage::deleteDirectory('public/' . $directory);
                Storage::disk('public')->put($filename, File::get($request->categoryImage));

                if (!is_dir(storage_path("app/public/productCategory/" . $categoryId . "/default"))) {
                    mkdir(storage_path("app/public/productCategory/" . $categoryId . "/default"), 0775, true);
                }

                $carouselPath1          = 'public/productCategory/' . $categoryId . '/default/' . $imagName;
                Image::make($request->file('categoryImage'))->save(storage_path('app/' . $carouselPath1));

                $categeryInfo->image    = $imagName;
            }

            if ($request->hasFile('categoryImageMedium')) {

                $imagName1               = time() . '_' . Str::replace(' ', "-", $request->categoryImageMedium->getClientOriginalName());
                $directory              = 'productCategory/' . $categoryId . '/medium';
                $filename               = $directory . '/' . $imagName1 . '/';
                Storage::deleteDirectory('public/' . $directory);
                Storage::disk('public')->put($filename, File::get($request->categoryImageMedium));

                if (!is_dir(storage_path("app/public/productCategory/" . $categoryId . "/medium"))) {
                    mkdir(storage_path("app/public/productCategory/" . $categoryId . "/medium"), 0775, true);
                }

                $imgpath1          = 'public/productCategory/' . $categoryId . '/medium/' . $imagName1;
                Image::make($request->file('categoryImageMedium'))->save(storage_path('app/' . $imgpath1));

                $categeryInfo->image_md    = $imagName1;
            }

            if ($request->hasFile('categoryImageSmall')) {

                $imagName2              = time() . '_' . Str::replace(' ', "-", $request->categoryImageSmall->getClientOriginalName());
                $directory              = 'productCategory/' . $categoryId . '/small';
                $filename               = $directory . '/' . $imagName2 . '/';
                Storage::deleteDirectory('public/' . $directory);
                Storage::disk('public')->put($filename, File::get($request->categoryImageSmall));

                if (!is_dir(storage_path("app/public/productCategory/" . $categoryId . "/small"))) {
                    mkdir(storage_path("app/public/productCategory/" . $categoryId . "/small"), 0775, true);
                }

                $path2          = 'public/productCategory/' . $categoryId . '/small/' . $imagName2;
                Image::make($request->file('categoryImageSmall'))->save(storage_path('app/' . $path2));

                $categeryInfo->image_sm    = $imagName2;
            }
            $categeryInfo->save();

            $meta_title = $request->meta_title;
            $meta_keywords = $request->meta_keywords;
            $meta_description = $request->meta_description;

            if (!empty($meta_title) || !empty($meta_keywords) || !empty($meta_description)) {
                CategoryMetaTags::where('category_id', $categoryId)->delete();
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
        return response()->json(['error' => $error, 'message' => $message, 'categoryId' => $categoryId]);
    }

    public function delete(Request $request)
    {
        $id         = $request->id;
        $info       = ProductCategory::find($id);
        $info->delete();
        $directory      = 'productCategory/' . $id;
        Storage::deleteDirectory($directory);
        // echo 1;
        return response()->json(['message' => "Successfully deleted!", 'status' => 1]);
    }

    public function changeStatus(Request $request)
    {

        $id             = $request->id;
        $status         = $request->status;
        $info           = ProductCategory::find($id);
        $info->status   = $status;
        $info->update();
        // echo 1;
        return response()->json(['message' => "You changed the status!", 'status' => 1]);
    }

    public function export()
    {
        return Excel::download(new ProductCategoryExport, 'productCategories.xlsx');
    }

    public function exportPdf()
    {
        $list       = ProductCategory::all();
        $pdf        = PDF::loadView('platform.exports.product.product_category_excel', array('list' => $list, 'from' => 'pdf'))->setPaper('a4', 'landscape');;
        return $pdf->download('productCategories.pdf');
    }
}
