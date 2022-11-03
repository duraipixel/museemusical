<?php

namespace App\Exports;


use App\Models\Category\MainCategory;
use App\Models\Offers\Coupons;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class CouponsExport implements FromView
{
    public function view(): View
    {
        $list = Coupons::select('coupons.*')->get();
        return view('platform.exports.coupon.excel', compact('list'));
    }
}
