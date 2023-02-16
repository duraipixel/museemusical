<?php

namespace App\Http\Controllers\Product;

use App\Exports\ProductExport;
use App\Http\Controllers\Controller;
use App\Imports\MultiSheetProductImport;
use App\Imports\TestImport;
use App\Imports\UploadAttributes;
use Illuminate\Http\Request;
use App\Models\Category\MainCategory;
use App\Models\Master\Brands;
use App\Models\Product\Product;
use App\Models\Product\ProductAttributeSet;
use App\Models\Product\ProductCategory;
use App\Models\Product\ProductCrossSaleRelation;
use App\Models\Product\ProductDiscount;
use App\Models\Product\ProductImage;
use App\Models\Product\ProductLink;
use App\Models\Product\ProductMeasurement;
use App\Models\Product\ProductMetaTag;
use App\Models\Product\ProductRelatedRelation;
use App\Models\Product\ProductWithAttributeSet;
use App\Repositories\ProductRepository;
use Illuminate\Support\Str;
use DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Image;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class ProductController extends Controller
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository    = $productRepository;
    }

    public function index(Request $request)
    {
        $title                  = "Product";
        $breadCrum              = array('Products', 'Product');
        
        if ($request->ajax()) {
            
            $f_product_category = $request->get('filter_product_category');
            $f_brand = $request->get('filter_brand');
            $f_label = $request->get('filter_label');
            $f_tags = $request->get('filter_tags');
            $f_stock_status = $request->get('filter_stock_status');
            $f_product_name = $request->get('filter_product_name');
            $f_product_status = $request->get('filter_product_status');
            $f_video_booking = $request->get('filter_video_booking');

            $data = Product::leftJoin('brands','brands.id','=','products.brand_id')->
            leftJoin('product_categories','product_categories.id','=','products.category_id')
            ->select('products.*','brands.brand_logo','brands.brand_name','product_categories.name as category')->when($f_product_category, function($q) use($f_product_category){
                return $q->where('category_id', $f_product_category);
            })
            ->when($f_brand, function($q) use($f_brand) {
                return $q->where('brands.id', $f_brand);
            })
            ->when($f_tags, function($q) use($f_tags) {
                return $q->where('tag_id', $f_tags);
            })
            ->when($f_stock_status, function($q) use($f_stock_status) {
                return $q->where('stock_status', $f_stock_status);
            })
            ->when($f_product_status, function($q) use($f_product_status) {
                return $q->where('products.status', $f_product_status);
            })
            ->when($f_video_booking, function($q) use($f_video_booking) {
                return $q->where('has_video_shopping', $f_video_booking);
            })
            ->when($f_product_name, function($q) use($f_product_name) {
                return $q->where(function($qr) use($f_product_name){
                    $qr->where('product_name', 'like', "%{$f_product_name}%")
                    ->orWhere('sku', 'like', "%{$f_product_name}%")
                    ->orWhere('price', 'like', "%{$f_product_name}%");
                });
            })
            ->when($f_label, function($q) use($f_label) {
                return $q->where('label_id', $f_label);
            });

            $keywords = $request->get('search')['value'];
            
            $datatables =  Datatables::of($data)
                ->filter(function ($query) use ($keywords) {

                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        $query->where(function($que) use($keywords, $date){
                            $que->where('has_video_shopping', 'like', "%{$keywords}%")
                                ->orWhere('products.status', 'like', "%{$keywords}%")
                                ->orWhere('products.stock_status', 'like', "%{$keywords}%")
                                ->orWhere('brands.brand_name', 'like', "%{$keywords}%")
                                ->orWhere('product_categories.name', 'like', "%{$keywords}%")
                                ->orWhere('products.product_name', 'like', "%{$keywords}%")
                                ->orWhere('products.sku', 'like', "%{$keywords}%")
                                ->orWhere('products.price', 'like', "%{$keywords}%")
                                ->orWhereDate("products.created_at", $date);
                        });
                        return $query;
                    }
                })
                ->addIndexColumn()
                ->editColumn('status', function($row){
                    $status = '<a href="javascript:void(0);" class="badge badge-light-'.(($row->status == 'published') ? 'success': 'danger').'" tooltip="Click to '.(($row->status == 'published') ? 'Unpublish' : 'Publish').'" onclick="return commonChangeStatus(' . $row->id . ', \''.(($row->status == 'published') ? 'unpublished': 'published').'\', \'products\')">'.ucfirst($row->status).'</a>';
                    return $status;
                })
                ->editColumn('stock_status', function($row){
                    return ucwords(str_replace( "_", " ", $row->stock_status ) );
                })
                // ->editColumn('brand', function($row){
                //     return $row->productBrand->brand_name ?? '';
                // })
                ->addColumn('action', function($row){
                    $edit_btn = '<a href="'.route('products.add.edit', ['id' => $row->id]).'" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                                    <i class="fa fa-edit"></i>
                                </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'products\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';

                    return $edit_btn . $del_btn;
                })
               
                ->rawColumns(['action', 'status', 'category']);
                
             
                return $datatables->make(true);
        }

        $addHref = route('products.add.edit');
        $uploadHref = route('products.upload');
        $routeValue = 'products';
        $productCategory        = ProductCategory::where('status', 'published')->get();
        $brands                 = Brands::where('status', 'published')->get();
        $productLabels          = MainCategory::where(['slug' => 'product-labels', 'status' => 'published'])->first();        
        $productTags            = MainCategory::where(['slug' => 'product-tags', 'status' => 'published'])->first();

        $params                 = array(
                                    'title' => $title,
                                    'breadCrum' => $breadCrum,
                                    'addHref' => $addHref,
                                    'uploadHref' => $uploadHref,
                                    'routeValue' => $routeValue,
                                    'productCategory' => $productCategory,
                                    'brands' => $brands,
                                    'productLabels' => $productLabels,
                                    'productTags' => $productTags,
                                );

        return view('platform.product.index', $params);
    }

    public function addEditPage(Request $request, $id = null )
    {
        
        $title                  = "Add Product";
        $breadCrum              = array('Products', 'Add Product');
        if( $id ) {
            $title              = 'Update Product';
            $breadCrum          = array('Products', 'Update Product');
            $info               = Product::find( $id );
        }
        $otherProducts          = Product::where('status', 'published')
                                        ->when($id, function ($q) use ($id) {
                                            return $q->where('id', '!=', $id);
                                        })->get();
        $productCategory        = ProductCategory::where('status', 'published')->get();
        $attributes             = ProductAttributeSet::where('status', 'published')->orderBy('order_by','ASC')->get();

        $productLabels          = MainCategory::where(['slug' => 'product-labels', 'status' => 'published'])->first();
        
        $productTags            = MainCategory::where(['slug' => 'product-tags', 'status' => 'published'])->first();
        $brands                 = Brands::where('status', 'published')->get();

        $images                 = $this->productRepository->getImageInfoJson($id);
        $brochures              = $this->productRepository->getBrochureJson($id);
        
        $params                 = array(

                                    'title' => $title,
                                    'breadCrum' => $breadCrum,
                                    'productCategory' => $productCategory,
                                    'productLabels' => $productLabels,
                                    'productTags' => $productTags,
                                    'brands' => $brands,
                                    'info'  => $info ?? '',
                                    'images' => $images,
                                    'brochures' => $brochures,
                                    'attributes' => $attributes,
                                    'otherProducts' => $otherProducts,
                                    
                                );
        
        return view('platform.product.form.add_edit_form', $params);
    }

    public function saveForm(Request $request)
    {
        $id                 = $request->id;
        $product_page_type  = $request->product_page_type;
        $isUpdate           = false;
        $validate_array     = [
                                'product_page_type' => 'required',
                                'category_id' => 'required',
                                'brand_id' => 'required',
                                'status' => 'required',
                                'stock_status' => 'required',
                                'product_name' => 'required_if:product_page_type,==,general',
                                'base_price' => 'required_if:product_page_type,==,general',
                                'sku' => 'required_if:product_page_type,==,general|unique:products,sku,' . $id . ',id,deleted_at,NULL',
                                'sale_price' => 'required_if:discount_option,==,percentage',
                                'sale_price' => 'required_if:discount_option,==,fixed_amount',
                                'sale_start_date' => 'required_if:sale_price,!=,0',
                                'sale_end_date' => 'required_if:sale_price,==,0',
                                'dicsounted_price' => 'required_if:discount_option,==,fixed_amount',
                                'filter_variation' => 'nullable|array',
                                'filter_variation.*' => 'nullable|required_with:filter_variation',
                                'filter_variation_value' => 'nullable|required_with:filter_variation|array',
                                'filter_variation_value.*' => 'nullable|required_with:filter_variation.*',
                               
                            ];
                                       
        if( isset($request->url) && !empty( $request->url) && !is_null($request->url[0]) ) {
            // $validate_array['url'] = 'nullable|url|array';
            // $validate_array['url.*'] = 'nullable|url|required_with:url';
            // $validate_array['url_type'] = 'nullable|required_with:url|array';
            // $validate_array['url_type.*'] = 'nullable|required_with:url.*';
              
            $validate_array['url.*'] = 'required|url';
            $validate_array['url_type.*'] = 'required';
        }   
        $validator      = Validator::make( $request->all(), $validate_array );

        if ($validator->passes()) {
           
            if( isset( $request->avatar_remove ) && !empty($request->avatar_remove) ) {
                $ins['base_image']          = null;
            }
            
            $ins[ 'product_name' ]          = $request->product_name;
            $ins[ 'hsn_code' ]              = $request->hsn_code;
            $ins[ 'product_url' ]           = Str::slug($request->product_name);
            $ins[ 'sku' ]                   = $request->sku;
            $ins[ 'price' ]                 = $request->base_price;
            $ins[ 'status' ]                = $request->status;
            $ins[ 'brand_id' ]              = $request->brand_id;
            $ins[ 'category_id' ]           = $request->category_id;
            $ins[ 'tag_id' ]                = $request->tag_id;
            $ins[ 'label_id' ]              = $request->label_id;
            $ins[ 'is_featured' ]           = $request->is_featured ?? 0;
            $ins[ 'has_video_shopping' ]    = $request->has_video_shopping ?? 'no';
            $ins[ 'quantity' ]              = $request->qty;
            $ins[ 'stock_status' ]          = $request->stock_status;
            $ins[ 'sale_price' ]            = $request->sale_price ?? 0;
            $ins[ 'sale_start_date' ]       = $request->sale_start_date ?? null;
            $ins[ 'sale_end_date' ]         = $request->sale_end_date ?? null;
            $ins[ 'description' ]           = $request->product_description ?? null;
            $ins[ 'technical_information' ] = $request->product_technical_information ?? null;
            $ins[ 'feature_information' ]   = $request->product_feature_information ?? null;
            $ins[ 'specification' ]         = $request->product_specification ?? null;
            $ins[ 'added_by' ]              = auth()->user()->id;
            
            $productInfo                    = Product::updateOrCreate(['id' => $id], $ins);
            if(!empty($id))
            {
                $message                    = "Thank you! You've updated Products";
                $isUpdate                   = true;
            }else{
                $message                    = "Thank you! You've add Products";
            }
            $product_id                     = $productInfo->id;
            if( $request->hasFile('avatar') ) {        
              
                $imageName                  = uniqid().Str::slug($request->avatar->getClientOriginalName());
                $directory                  = 'products/'.$product_id.'/default';
                Storage::deleteDirectory('public/'.$directory);

                if (!is_dir(storage_path("app/public/products/".$product_id."/default"))) {
                    mkdir(storage_path("app/public/products/".$product_id."/default"), 0775, true);
                }

                $fileNameThumb              = 'public/products/'.$product_id.'/default/335_225_px_' . time() . '-' . $imageName;
                Image::make($request->avatar)->resize(280,190)->save(storage_path('app/' . $fileNameThumb));

                $productInfo->base_image    = $fileNameThumb;
                $productInfo->update();

            }
            
            ProductDiscount::where('product_id', $product_id )->delete();
            if( isset( $request->discount_option ) && $request->discount_option != 1 ) {
                $disIns['product_id'] = $product_id;
                $disIns['discount_type'] = $request->discount_option;
                $disIns['discount_value'] = $request->discount_percentage ?? 0; //this is for percentage 
                $disIns['amount'] = $request->dicsounted_price ?? 0; //this only for fixed amount
                ProductDiscount::create($disIns);
            }

            ProductMeasurement::where('product_id', $product_id )->delete();
            if( isset( $request->isShipping ) ) {

                $measure['product_id']  = $product_id;
                $measure[ 'weight' ]    = $request->weight ?? 0;
                $measure[ 'width' ]     = $request->width ?? 0;
                $measure[ 'hight' ]     = $request->height ?? 0;
                $measure[ 'length' ]    = $request->length ?? 0;
                ProductMeasurement::create($measure);
            }

            $request->session()->put('image_product_id', $product_id);
            $request->session()->put('brochure_product_id', $product_id);
            
            if( isset( $request->filter_variation ) && !empty( $request->filter_variation ) )  {
                $filter_variation = $request->filter_variation;
                $filter_variation_value = $request->filter_variation_value;
                $filter_variation_title = $request->filter_variation_title;
                ProductWithAttributeSet::where('product_id', $product_id)->delete();

                for ($i=0; $i < count($request->filter_variation); $i++) { 
                    $insAttr = [];
                    $insAttr['product_attribute_set_id']    = $filter_variation[$i];
                    $insAttr['attribute_values']            = $filter_variation_value[$i];
                    $insAttr['title']                       = $filter_variation_title[$i];
                    $insAttr['product_id']                  = $product_id;

                    ProductWithAttributeSet::create($insAttr);
                }
               
            } 
            
            $meta_ins['meta_title']         = $request->meta_title ?? '';
            $meta_ins['meta_description']   = $request->meta_description ?? '';
            $meta_ins['meta_keyword']       = $request->meta_keywords ?? '';
            $meta_ins['product_id']         = $product_id;
            ProductMetaTag::updateOrCreate(['product_id' => $product_id], $meta_ins);

            if( isset($request->related_product) && !empty($request->related_product) ) {
                ProductRelatedRelation::where('from_product_id', $product_id)->delete();
                foreach ( $request->related_product as $proItem ) {
                    $insRelated['from_product_id'] = $product_id;
                    $insRelated['to_product_id'] = $proItem;
                    ProductRelatedRelation::create($insRelated);
                }
            }

            if( isset($request->cross_selling_product) && !empty($request->cross_selling_product) ) {
                ProductCrossSaleRelation::where('from_product_id', $product_id)->delete();
                foreach ( $request->cross_selling_product as $proItem ) {
                    $insCrossRelated['from_product_id'] = $product_id;
                    $insCrossRelated['to_product_id'] = $proItem;
                    ProductCrossSaleRelation::create($insCrossRelated);
                }
            }

            if( isset( $request->url ) && !empty( $request->url ) && !is_null($request->url[0]) )  {

                $url = $request->url;
                $url_type = $request->url_type;
                
                // $linkArr                        = array_combine($url_type, $url);
                
                if( isset( $url ) && !empty( $url )) {
                    
                    ProductLink::where('product_id', $product_id)->delete();
                    for ($i=0; $i < count($url); $i++) { 
                        $insAttr = [];
                        $insAttr['url']         = $url[$i];
                        $insAttr['url_type']    = $url_type[$i];
                        $insAttr['product_id']  = $product_id;

                        ProductLink::create($insAttr);
                    }
                    
                }

            } 
            
            $error                          = 0;
        } else {

            $error                          = 1;
            $message                        = errorArrays($validator->errors()->all());

            $product_id                     = '';

        } 
        return response()->json(['error' => $error, 'isUpdate' => $isUpdate, 'message' => $message, 'product_id' => $product_id]);
    }

    public function uploadGallery(Request $request)
    {
        
        $product_id = $request->session()->pull('image_product_id');
        if( $request->hasFile('file') && isset( $product_id ) ) {
            $files = $request->file('file');
            $imageIns = [];
            $iteration = 1;
            foreach ($files as $file) {
                $imageName = uniqid().Str::slug($file->getClientOriginalName());
                if (!is_dir(storage_path("app/public/products/".$product_id."/thumbnail"))) {
                    mkdir(storage_path("app/public/products/".$product_id."/thumbnail"), 0775, true);
                }
                
                if (!is_dir(storage_path("app/public/products/".$product_id."/gallery"))) {
                    mkdir(storage_path("app/public/products/".$product_id."/gallery"), 0775, true);
                }
                if (!is_dir(storage_path("app/public/products/".$product_id."/detailPreview"))) {
                    mkdir(storage_path("app/public/products/".$product_id."/detailPreview"), 0775, true);
                }

                $fileNameThumb =  'public/products/'.$product_id.'/thumbnail/100_100_px_' . time() . '-' . $imageName;
                Image::make($file)->resize(120,120)->save(storage_path('app/' . $fileNameThumb));

                
                $fileSize = $file->getSize();

                $fileName =  'public/products/'.$product_id.'/gallery/1000_700_px_' . time() . '-' . $imageName;
                Image::make($file)->resize(1000,700)->save(storage_path('app/' . $fileName));

                $fileNamePreview = 'public/products/'.$product_id.'/detailPreview/615_450_px_' . time() . '-' . $imageName;
                Image::make($file)->resize(615,450)->save(storage_path('app/' . $fileNamePreview));

                $imageIns[] = array( 
                    'gallery_path'  => $fileName, 
                    'image_path'    => $fileNameThumb,
                    'preview_path'  => $fileNamePreview,
                    'product_id'    => $product_id,
                    'file_size'     => $fileSize,
                    'is_default'    => ($iteration == 1) ? 1: "0",
                    'order_by'      => $iteration,
                    'status'        => 'published'
                );

                $iteration++;

            }
            if( !empty( $imageIns ) ) {
                
                ProductImage::insert($imageIns);
                echo 'Uploaded';
            }

            $request->session()->forget('image_product_id');
        } else {
            echo 'upload error';
        }
    }

    public function removeImage(Request $request)
    {

        $id             = $request->id;
        $info           = ProductImage::find( $id );
        
        $directory      = 'public/products/'.$info->product_id.'/detailPreview/'.$info->preview_path;
        Storage::delete($directory);
        
        $directory      = 'products/'.$info->info.'/gallery/'.$info->gallery_path;
        Storage::delete('public/'.$directory);

        $directory      = 'products/'.$info->info.'/thumbnail/'.$info->image_path;
        Storage::delete('public/'.$directory);

        $info->delete();
        echo 1;
        return true;

    }

    public function uploadBrochure(Request $request)
    {
        
        $product_id = $request->session()->pull('brochure_product_id');
        if( $request->hasFile('file') && isset( $product_id ) ) {
            
            $filename       = time() . '_' . $request->file->getClientOriginalName();
            $directory      = 'products/'.$product_id.'/brochure';
            $filename       = $directory.'/'.$filename;
            Storage::deleteDirectory('public/'.$directory);

            if (!is_dir(storage_path("app/public/products/".$product_id."/brochure"))) {
                mkdir(storage_path("app/public/products/".$product_id."/brochure"), 0775, true);
            }
           
            Storage::disk('public')->put($filename, File::get($request->file));

            $info = Product::find( $product_id );
            $info->brochure_upload = $filename;
            $info->update();

        }
        echo 1;
    }

    public function removeBrochure(Request $request)
    {

        $id             = $request->id;
        $info           = Product::find( $id );
        
        $directory      = 'products/'.$id.'/brochure';
        Storage::deleteDirectory('public/'.$directory);

        $info->brochure_upload = null;
        $info->update();
        echo 1;
        return true;

    }

    public function changeStatus(Request $request)
    {
        $id             = $request->id;
        $status         = $request->status;
        $info           = Product::find($id);
        $info->status   = $status;
        $info->update();
        
        return response()->json(['message'=>"You changed the Product status!",'status' => 1 ] );

    }

    public function delete(Request $request)
    {
        $id         = $request->id;
        $info       = Product::find($id);
        $info->delete();
        
        return response()->json(['message'=>"Successfully deleted Product!",'status' => 1 ] );
    }

    public function export()
    {
        return Excel::download(new ProductExport, 'products.xlsx');
    }

    public function exportPdf()
    {
        $list       = Product::all();
        $pdf        = PDF::loadView('platform.exports.product.products_excel', array('list' => $list, 'from' => 'pdf'))->setPaper('a2', 'landscape');;
        return $pdf->download('products.pdf');
    }

    public function bulkUpload(Request $request)
    {

        $addHref        = route('products.add.edit');
        $uploadHref     = route('products.upload');
        $title          = "Product Bulk Upload";
        $breadCrum      = array('Products', 'Product Bulk Upload');

        $params         = array(
            'addHref' => $addHref,
            'uploadHref' => $uploadHref,
            'title' => $title,
            'breadCrum' => $breadCrum,
        );    

        return view('platform.product.bulk_upload', $params);
    }

    public function doBulkUpload(Request $request)
    {
        Excel::import( new MultiSheetProductImport, request()->file('file') );
        return response()->json(['error'=> 0, 'message' => 'Imported successfully']);
    }

    public function doAttributesBulkUpload(Request $request)
    {
        Excel::import( new UploadAttributes, request()->file('file') );
        return response()->json(['error'=> 0, 'message' => 'Imported successfully']);
    }

    public function setImageOrder(Request $request)
    {

        $image_id = $request->image_id;
        $order_by = $request->order_by;
        $image_info = ProductImage::find($image_id);
        $image_info->order_by = $order_by;
        $image_info->update();

        return response()->json(['message'=>"Successfully updated!",'status' => 1 ] );

    }

}
