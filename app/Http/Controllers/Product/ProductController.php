<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category\MainCategory;
use App\Models\Master\Brands;
use App\Models\Product\Product;
use App\Models\Product\ProductCategory;
use App\Models\Product\ProductImage;
use App\Repositories\ProductRepository;
use Illuminate\Support\Str;
use DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Image;

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
                ->addColumn('action', function($row){
                    $edit_btn = '<a href="'.route('products.add.edit', ['id' => $row->id]).'" target="_blank" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                                    <i class="fa fa-edit"></i>
                                </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'products\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';

                    return $edit_btn . $del_btn;
                })
               
                ->rawColumns(['action', 'status']);
                
             
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
        $productCategory        = ProductCategory::where('status', 'published')->get();

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
                                    'brochures' => $brochures
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
                            'tag_id' => 'required',
                            'label_id' => 'required',
                            'status' => 'required',
                            'product_name' => 'required_if:product_page_type,==,general',
                            'base_price' => 'required_if:product_page_type,==,general',
                            'sku' => 'required_if:product_page_type,==,general|unique:products,sku,' . $id . ',id,deleted_at,NULL',
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

            $request->session()->put('image_product_id', $product_id);
            $request->session()->put('brochure_product_id', $product_id);
            
            $error                          = 0;
            $message                        = '';

        } else {

            $error                          = 1;
            $message                        = $validator->errors()->all();
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

}
