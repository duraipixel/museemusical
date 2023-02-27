<?php

namespace App\Imports;

use App\Models\Product\Product;
use App\Models\Product\ProductAttributeSet;
use App\Models\Product\ProductMapAttribute;
use App\Models\Product\ProductWithAttributeSet;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class UploadAttributes implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {

        $sku = $row['sku'];
        if( isset( $sku ) && !empty( $sku ) ) {
            $product_info = Product::select('id', 'category_id')->where('sku', $row['sku'])->first();
            
            $category_id = $product_info->productCategory->parent_id ?? $product_info->productCategory->id;

            if( !empty( $category_id )) {
                
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
    
                    ProductWithAttributeSet::updateOrCreate(['product_id' => $product_info->id, 'product_attribute_set_id' => $attribute_info->id, 'title' => $row['keys'] ], $ins_set);
                }

            }
        }
    }
}
