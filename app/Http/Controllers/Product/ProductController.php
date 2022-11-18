<?php

namespace App\Http\Controllers\Product;

use App\Exports\ProductExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category\MainCategory;
use App\Models\Master\Brands;
use App\Models\Product\Product;
use App\Models\Product\ProductAttributeSet;
use App\Models\Product\ProductCategory;
use App\Models\Product\ProductCrossSaleRelation;
use App\Models\Product\ProductDiscount;
use App\Models\Product\ProductImage;
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
use Excel;
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

            $data = Product::all();
            $status = $request->get('status');
            $keywords = $request->get('search')['value'];
            
            $datatables =  Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function($row){
                    $status = '<a href="javascript:void(0);" class="badge badge-light-'.(($row->status == 'published') ? 'success': 'danger').'" tooltip="Click to '.(($row->status == 'published') ? 'Unpublish' : 'Publish').'" onclick="return commonChangeStatus(' . $row->id . ', \''.(($row->status == 'published') ? 'unpublished': 'published').'\', \'products\')">'.ucfirst($row->status).'</a>';
                    return $status;
                })
                ->addColumn('category', function($row){
                    return $row->productCategory->name ?? '';
                })
                ->addColumn('brand', function($row){
                    return $row->productBrand->brand_name ?? '';
                })
               
                ->addColumn('action', function($row){
                    $edit_btn = '<a href="'.route('products.add.edit', ['id' => $row->id]).'" target="_blank" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                                    <i class="fa fa-edit"></i>
                                </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'products\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';

                    return $edit_btn . $del_btn;
                })
               
                ->rawColumns(['action', 'status', 'category', 'brand']);
                
             
                return $datatables->make(true);
        }


        return view('platform.product.index', compact('title', 'breadCrum'));
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

        $validator      = Validator::make($request->all(), [
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

                        ]);

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
            $product_id                     = $productInfo->id;
            if( $request->hasFile('avatar') ) {        
              
                $imageName                  = uniqid().$request->avatar->getClientOriginalName();
                $directory                  = 'products/'.$product_id.'/default';
                Storage::deleteDirectory('public/'.$directory);

                if (!is_dir(storage_path("app/public/products/".$product_id."/default"))) {
                    mkdir(storage_path("app/public/products/".$product_id."/default"), 0775, true);
                }

                $fileNameThumb              = 'public/products/'.$product_id.'/default/335_225_px_' . time() . '-' . $imageName;
                Image::make($request->avatar)->resize(120,120)->save(storage_path('app/' . $fileNameThumb));

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
                $proAttributes              = array_combine($request->filter_variation, $request->filter_variation_value);
                
                if( isset( $proAttributes ) && !empty( $proAttributes )) {
                    ProductWithAttributeSet::where('product_id', $product_id)->delete();
                    foreach ( $proAttributes as $akey => $avalue ) {

                        $insAttr['product_attribute_set_id']    = $akey;
                        $insAttr['attribute_values']            = $avalue;
                        $insAttr['product_id']                  = $product_id;

                        ProductWithAttributeSet::create($insAttr);

                    }
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
            
            $error                          = 0;
            $message                        = '';

        } else {

            $error                          = 1;
            $message                        = errorArrays($validator->errors()->all());

            $product_id                     = '';

        }
        return response()->json(['error' => $error, 'message' => $message, 'product_id' => $product_id]);
    }

    public function uploadGallery(Request $request)
    {
        
        $product_id = $request->session()->pull('image_product_id');
        if( $request->hasFile('file') && isset( $product_id ) ) {
            $files = $request->file('file');
            $imageIns = [];
            foreach ($files as $file) {
                $imageName = uniqid().$file->getClientOriginalName();
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
                    'is_default'    => "0",
                    'status'        => 'published'
                );

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

}
