<?php

namespace App\Exports;


use App\Models\Category\MainCategory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class MainCategoryExport implements FromView
{
    public function view(): View
    {
        $list = MainCategory::select('main_categories.*', 'users.name as users_name',DB::raw(" IF(main_categories.status = 2, 'Inactive', 'Active') as user_status"))->join('users', 'users.id', '=', 'main_categories.added_by')->get();
        return view('platform.exports.category.excel', compact('list'));
    }
}
