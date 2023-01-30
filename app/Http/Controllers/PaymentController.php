<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Payment::selectRaw('mm_payments.order_id,mm_payments.payment_no,mm_payments.status as payment_status,mm_orders.*,sum(mm_order_products.quantity) as order_quantity')
                            ->join('order_products', 'order_products.order_id', '=', 'orders.id')
                            ->join('payments', 'payments.order_id', '=', 'orders.id')
                            ->groupBy('orders.id')->orderBy('orders.id', 'desc');
            $filter_subCategory   = '';
            $status = $request->get('status');
            $keywords = $request->get('search')['value'];
            $datatables =  DataTables::of($data)
                ->filter(function ($query) use ($keywords, $status,$filter_subCategory) {
                    if ($status) {
                        return $query->where('orders.status', 'like', $status);
                    }
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        return $query->where('orders.billing_name','like',"%{$keywords}%")
                                ->orWhere('orders.billing_email', 'like', "%{$keywords}%")
                                ->orWhere('orders.billing_mobile_no', 'like', "%{$keywords}%")
                                ->orWhere('orders.billing_address_line1', 'like', "%{$keywords}%")
                                ->orWhere('orders.billing_state', 'like', "%{$keywords}%")
                                ->orWhere('orders.status', 'like', "%{$keywords}%")
                                ->orWhereDate("orders.created_at", $date);
                    }
                })
                ->addIndexColumn()
                ->editColumn('billing_info', function ($row) {
                    $billing_info = '';
                    $billing_info .= '<div class="font-weight-bold">'.$row['billing_name'].'</div>';
                    $billing_info .= '<div class="">'.$row['billing_email'].','.$row['billing_mobile_no'].'</div>';
                    $billing_info .= '<div class="">'.$row['billing_address_line1'].'</div>';

                    return $billing_info;
                })
               
                ->editColumn('payment_status', function ($row) {
                    return ucwords($row->payment_status);
                })
                ->editColumn('order_status', function ($row) {
                    return ucwords($row->status);
                })
                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })
             
                ->addColumn('action', function ($row) {
                    $view_btn = '<a href="javascript:void(0)" onclick="return viewOrder('.$row->id.')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                                    <i class="fa fa-eye"></i>
                                </a>';

                    return $view_btn;
                })
                ->rawColumns(['action', 'status', 'billing_info', 'payment_status', 'order_status', 'created_at']);
            return $datatables->make(true);
        }
        $breadCrum = array('Payments');
        $title      = 'Payment';
        return view('platform.order.index',compact('title','breadCrum'));
    }
}
