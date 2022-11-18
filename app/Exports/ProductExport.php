<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

use App\Models\Product\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class ProductExport implements FromView
{
    public function view(): View
    {
        $list = Product::all();
        
        return view('platform.exports.product.products_excel', compact('list'));
    }
}
