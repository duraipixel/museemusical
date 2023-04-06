<?php

namespace App\Http\Controllers;

use App\Models\Product\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

use Image;

class ImageUploadController extends Controller
{
    public function index()
    {
        $path = public_path('Images');
        // $files = Storage::allFiles($directory);
        $files = File::allFiles($path);

        if( $files ) {
            foreach ($files as $item) {
                # code...
                $file_name = $item->getFilename();
                if( $file_name ) {
                    $name = explode('-', $file_name);
                    
                    $sku = current($name);
                    dump( $sku );
                    if( isset( $sku ) && !empty( $sku ) ) {
                        $product_info = Product::where('sku', $sku)->first();
                        if( isset( $product_info ) && !empty( $product_info ) ){
                            $product_id = $product_info->id;
                            
                            /** upload image */
                            $imageName                  = uniqid().$sku;
                            $directory                  = 'products/'.$product_id.'/default';
                            Storage::deleteDirectory('public/'.$directory);

                            if (!is_dir(storage_path("app/public/products/".$product_id."/default"))) {
                                mkdir(storage_path("app/public/products/".$product_id."/default"), 0775, true);
                            }

                            $fileNameThumb              = 'public/products/'.$product_id.'/default/' .  $imageName;
                            Image::make($item)->save(storage_path('app/' . $fileNameThumb));

                            $product_info->base_image    = $fileNameThumb;
                            $product_info->update();
                            // dd( $product_info );

                        }
                    }
                }
                
            }
        }
  
        // dd($files);
    }

    
}
