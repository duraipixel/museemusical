<?php

namespace App\Http\Controllers\Product;

use App\Exports\ComboProductExport;
use App\Http\Controllers\Controller;
use App\Models\Combo;
use App\Models\Product\ComboProduct;
use App\Models\Product\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DataTables;
use Excel;
use PDF;
use Illuminate\Support\Str;

class ComboProductController extends Controller
{
    public function index(Request $request)
    {
        $title                  = "Combo Collections";
        $breadCrum              = array('Combo', 'Combo Collections');

        if ($request->ajax()) {
            $data               = Combo::select('combos.*');
            $status             = $request->get('status');
            $keywords           = $request->get('search')['value'];
            $datatables         = Datatables::of($data)
            ->filter(function ($query) use ($keywords, $status) {
                if ($status) {
                    return $query->where('combos.status',$status);
                }
                if ($keywords) {
                    
                    if( !strpos($keywords, '.')) {
                        $date = date('Y-m-d', strtotime($keywords));
                    } 
                    $query->where('combos.combo_name', 'like', "%{$keywords}%");
                    if( isset( $date )) {
                        $query->orWhereDate("combos.created_at", $date);
                    }
                    
                    return $query;
                }
            })
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    $status = '<a href="javascript:void(0);" class="badge badge-light-'.(($row->status == 'published') ? 'success': 'danger').'" tooltip="Click to '.(($row->status == 'published') ? 'Unpublish' : 'Publish').'" onclick="return commonChangeStatus(' . $row->id . ', \''.(($row->status == 'published') ? 'unpublished': 'published').'\', \'combo-product\')">'.ucfirst($row->status).'</a>';
                    return $status;
                })
                ->addColumn('no_of_products', function ($row) {
                    return count($row->collectionProducts);
                })
                ->addColumn('action', function ($row) {
                    $edit_btn = '<a href="javascript:void(0);" onclick="return  openForm(\'combo-product\',' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                                    <i class="fa fa-edit"></i>
                                </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'combo-product\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                                <i class="fa fa-trash"></i></a>';
                    return $edit_btn . $del_btn;
                })
                ->rawColumns(['action', 'status', 'no_of_products']);
            return $datatables->make(true);
        }
        return view('platform.combo_product.index', compact('title','breadCrum'));
    }
    public function modalAddEdit(Request $request)
    {
        
        $title              = "Add Combo Product";
        $breadCrum          = array('Combo Products', 'Add Combo Product');

        $id                 = $request->id;
        $from               = $request->from;
        $info               = '';
        $modal_title        = 'Add Combo Product';
        $products           = Product::where('status', 'published')
                                ->when($id != '', function($q) use($id){
                                    $q->whereRaw('id not IN(SELECT product_id FROM `mm_combo_products` where combo_product_id  != '.$id.')');
                                } )
                                ->when($id == '', function($q){
                                    $q->whereRaw('id not IN(SELECT product_id FROM `mm_combo_products`)');
                                } )
                                ->get();
        

        if (isset($id) && !empty($id)) {
            $info           = Combo::find($id);
            
            $modal_title    = 'Update Combo Product';
        }
        
      
        
        return view('platform.combo_product.add_edit_modal', compact('modal_title', 'breadCrum', 'info', 'products'));
    }
    public function saveForm(Request $request,$id = null)
    {
        $id             = $request->id;
        $validator      = Validator::make($request->all(), [
                            'combo_name' => 'required|string|unique:combos,combo_name,' . $id,
                            'collection_product' => 'required|array|min:5',
                        ]);

        $categoryId         = '';
        if ($validator->passes()) {
            
            $ins['combo_name']     = $request->combo_name;
            $ins['order_by']            = $request->order_by;
            $ins['tag_line']            = $request->tag_line;
            $ins['show_home_page']      = $request->show_home_page ?? 'no';
            $ins['slug']                = Str::slug($request->combo_name);

            if($request->status)
            {
                $ins['status']          = 'published';
            } else {
                $ins['status']          = 'unpublished';
            }
            $error                      = 0;
            // dd( $ins );
            $collectionInfo             = Combo::updateOrCreate(['id' => $id], $ins);
            $collection_id              = $collectionInfo->id;
            
            if( isset($request->collection_product) && !empty($request->collection_product) ) {
                ComboProduct::where('combo_product_id', $collection_id)->delete();
                $iteration              = 1;
                foreach ( $request->collection_product as $proItem ) {
                    $insRelated['combo_product_id'] = $collection_id;
                    $insRelated['product_id']   = $proItem;
                    $insRelated['order_by']     = $iteration;
                    ComboProduct::create($insRelated);
                    $iteration++;
                }
            }
       
            $message                    = (isset($id) && !empty($id)) ? 'Updated Successfully' : 'Added successfully';
        } else {
            $error      = 1;
            $message    = $validator->errors()->all();
        }
        return response()->json(['error' => $error, 'message' => $message, 'categoryId' => $categoryId, 'from' => $request->from ?? '']);
    }
    
    public function delete(Request $request)
    {
        $id         = $request->id;
        $info       = Combo::find($id);
        $info->forceDelete();    
        return response()->json(['message'=>"Successfully deleted state!",'status'=>1]);
    }

    public function changeStatus(Request $request)
    {
        
        $id             = $request->id;
        $status         = $request->status;
        $info           = Combo::find($id);
        $info->status   = $status;
        $info->update();
        
        return response()->json(['message'=>"You changed the status!",'status'=>1]);

    }

    public function export()
    {
        return Excel::download(new ComboProductExport, 'comboProdcutCollections.xlsx');
    }
    
   
}
