<?php

namespace App\Imports;

use App\Models\Product\Product;
use App\Models\Product\ProductAttributeSet;
use App\Models\Product\ProductMapAttribute;
use App\Models\Product\ProductWithAttributeSet;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redirect;

class UploadAttributes implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading 
{
    /*public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                $totalRows = $event->getReader()->getTotalRows();
                $total_count='';
                foreach($totalRows as $x => $totalrow)
                {
                    $total_count=$totalRows[$x];
                }
                if (!empty($totalRows)) {
                    $row_count=$totalRows['MMPL-Sheet1'];
                    if($row_count>5001)
                    {
                        dd('hi2213232223233');
                        $data='hhh';
                        return Redirect::route('products')->with( ['data' => $data] );
                    }
                    else
                    {
                        echo 'less Count '.$totalRows['MMPL-Sheet1'];
                    }
                   
                }
            }
        ];
    }*/
    public function model(array $row)
    {
        $sku = $row['sku'];
        if( isset( $sku ) && !empty( $sku ) ) {
            $product_info = Product::where('sku', $row['sku'])->first();
            
            $category_id = $product_info->productCategory->parent_id ?? $product_info->productCategory->id ?? '';

            if( !empty( $category_id ) && isset( $row['header'] ) && !empty( $row['header'] ) ) {
                
                $attribute_set_name = $row['header'];
                $ins = [];
                $attr_slug = Str::slug($attribute_set_name);
                $ins['title'] = $attribute_set_name;
                $ins['slug'] = $attr_slug;
                $ins['product_category_id'] = $category_id;
                $ins['is_searchable'] = strtolower($row['is_searchable']) == 'yes' ? 1: 0;
                $ins['is_comparable'] = strtolower($row['is_searchable']) == 'yes' ? 1: 0;
                $ins['is_use_in_product_listing'] = strtolower($row['is_searchable']) == 'yes' ? 1: 0;
                $ins['status'] = 'published';

                ProductAttributeSet::updateOrCreate(['slug' => $attr_slug], $ins);

                $attribute_info = ProductAttributeSet::where('slug', $attr_slug)->first();
                if( !empty( $row['keys'] ) && !empty( $row['values'] ) ) {

                    $check = ProductMapAttribute::where('product_id', $product_info->id)->where('attribute_id', $attribute_info->id)->first();
                    if( isset($check) && !empty( $check ) ) {
                        $map_id = $check->id;
                    } else {

                        $atIns['product_id'] = $product_info->id;
                        $atIns['attribute_id'] = $attribute_info->id;
                        $map_id = ProductMapAttribute::create($atIns)->id;
                    }

                    $ins_set = [];
                    $ins_set['product_id'] = $product_info->id;
                    $ins_set['product_attribute_set_id'] = $map_id;
                    $ins_set['title'] = $row['keys'];
                    $ins_set['attribute_values'] = $row['values'];
                    $ins_set['status'] = 'published';
    
                    $attr = ProductWithAttributeSet::updateOrCreate(['product_id' => $product_info->id, 'product_attribute_set_id' => $attribute_info->id, 'title' => $row['keys'] ], $ins_set);
                    echo '<br>';
                    dump( $attr );
                }

            }
        }
    }
    public function batchSize(): int
    {
        return 10;
    }
    
    public function chunkSize(): int
    {
        return 10;
    }
}
