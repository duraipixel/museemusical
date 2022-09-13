<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    public function index(Request $request) {
        // echo 'test';
        // $info = DB::table('dbo.users')->get();
        // dd( $info );
    }
}
