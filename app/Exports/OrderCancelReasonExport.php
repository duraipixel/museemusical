<?php

namespace App\Exports;

use App\Models\OrderCancelReason;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class OrderCancelReasonExport implements FromView
{
    public function view(): View
    {
        $list = OrderCancelReason::get();
        return view('platform.exports.order_cancel.excel', compact('list'));
    }
}
