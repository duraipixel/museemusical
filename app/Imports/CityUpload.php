<?php

namespace App\Imports;

use App\Models\City;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CityUpload implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $city = $row['city'];
        if(isset($city) && !empty($city))
        {
            $ins = [];
            $ins['city']      = $city;
            $ins['state_code']      = $row['code'];
        }
        dump($row);
    }
}
