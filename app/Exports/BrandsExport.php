<?php

namespace App\Exports;


use App\Models\Master\Brands;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class BrandsExport implements FromView
{
    public function view(): View
    {
        $list = Brands::select('brands.*','users.name as users_name',DB::raw(" IF(brands.status = 2, 'Inactive', 'Active') as user_status"))->join('users', 'users.id', '=', 'brands.added_by')->get();
        // dd($list);
        return view('platform.exports.brand.excel', compact('list'));
    }
}
