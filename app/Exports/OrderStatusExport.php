<?php

namespace App\Exports;


use App\Models\Master\OrderStatus;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class OrderStatusExport implements FromView
{
    public function view(): View
    {
        $list = OrderStatus::select('order_statuses.status_name','order_statuses.description','order_statuses.created_at','order_statuses.order','users.name as users_name', DB::raw(" IF(order_statuses.status = 2, 'Inactive', 'Active') as user_status"))->join('users', 'users.id', '=', 'order_statuses.added_by')->get();
        
        // dd($list[0]->added); 
        return view('platform.exports.order_status.excel', compact('list'));
    }
}
