<?php

namespace App\Exports;

use App\Models\Combo;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ComboProductExport implements FromView
{
    public function view(): View
    {
        $list = Combo::all();
        
        return view('platform.exports.product.combo_product_excel', compact('list'));
    }
}
