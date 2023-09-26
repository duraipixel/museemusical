<?php

namespace App\Http\Controllers;

use App\Exports\NewsletterExport;
use App\Models\Newsletter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;
use Excel;

class NewsletterController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data       = Newsletter::whereNotNUll('email');
            $status     = $request->get('status');
            $keywords   = $request->get('search')['value'];
            $datatables =  DataTables::of($data)
                ->filter(function ($query) use ($keywords) {
                    
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        return $query->where('email', 'like', "%{$keywords}%")->orWhereDate("created_at", $date);
                    }
                })
                ->addIndexColumn()
               
                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })
    
                ->addColumn('action', function ($row) {
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'newsletter\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';
    
                    return $del_btn;
                })
                ->rawColumns(['action', 'status', 'created_at']);
            return $datatables->make(true);
        }
        $title                  = "Newsletter";
        $breadCrum              = array('Newsletter');
        return view('platform.newsletter.index', compact('title', 'breadCrum'));
    }

    public function delete(Request $request)
    {
        $id         = $request->id;
        $info       = Newsletter::find($id);
        $info->delete();
        return response()->json(['message'=>"Successfully deleted Newsletter!",'status'=>1]);
    }

    public function export()
    {
        return Excel::download(new NewsletterExport, 'newsletter.xlsx');
    }


    
}
