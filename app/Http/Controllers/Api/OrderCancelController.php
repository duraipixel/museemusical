<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrderCancelReason;
use Illuminate\Http\Request;

class OrderCancelController extends Controller
{
    public function index()
    {
        $data = OrderCancelReason::where('status','published')->orderBy('order_by','asc')->get();
        return response()->json(['data'=>$data]); 
    }
}
