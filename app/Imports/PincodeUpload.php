<?php

namespace App\Imports;

use App\Models\Master\Pincode;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PincodeUpload implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
       $pincode = $row['pincode'];
       if(isset($pincode) && !empty($pincode))
       {
            $ins = [];
            $ins['pincode']     = $pincode;
            $ins['description'] = $row['description'];
            $ins['status']      = strtolower($row['status']) == 'yes' ? 1: 2;
            $ins['added_by']        = Auth::id();   
            $attr = Pincode::updateOrCreate(['pincode'=>$pincode],$ins);
         
       }

    }
}
