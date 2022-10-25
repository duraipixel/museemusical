<?php

namespace App\Exports;


use App\Models\Category\SubCategory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class SubCategoryExport implements FromView
{
    public function view(): View
    {
        $list = SubCategory::select('sub_categories.*','main_categories.category_name as category_name', 'users.name as users_name')->join('main_categories', 'sub_categories.parent_id', '=', 'main_categories.id')->join('users', 'users.id', '=', 'sub_categories.added_by')->get();
        return view('platform.exports.sub_category.excel', compact('list'));
    }
}
