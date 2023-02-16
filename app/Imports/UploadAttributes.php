<?php

namespace App\Imports;

use App\Models\Product\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UploadAttributes implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {

        $sku = $row['sku'];
        if( isset( $sku ) && !empty( $sku ) ) {
            $product_info = Product::select('id', 'category_id')->where('sku', $row['sku'])->first();
            
            $category_id = $product_info->productCategory->parent_id ?? $product_info->productCategory->id;

            if( !empty( $category_id )) {
                dump( $row );
                dd( $category_id );
            }
        }
    }
}
