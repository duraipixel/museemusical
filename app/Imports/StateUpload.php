<?php

namespace App\Imports;

use App\Models\Master\Country;
use App\Models\Master\State;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PHPUnit\Framework\Constraint\Count;

class StateUpload implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $state_name = $row['state'];
        if(isset($state_name) && !empty($state_name))
        {
                $ins = [];
                $ins['state_name']      = $state_name;
                $ins['state_code']      = $row['code'];

                $country_id = Country::where('name',trim($row['country']))->first();

                $ins['country_id']      = $country_id->id ?? '';
                $ins['status']          = strtolower($row['status']) == 'yes' ? 1: 2;
                $ins['added_by']        = Auth::id();   
                $attr = State::updateOrCreate(['state_name'=>$state_name],$ins);
        }
    }
}
