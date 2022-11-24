<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use App\Exports\BannerExport;
use Illuminate\Support\Facades\Validator;
use DataTables;
use Carbon\Carbon;
use Auth;
use Excel;
use Illuminate\Support\Facades\Storage;
use PDF;
use Image;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $title = "Banner";
        if ($request->ajax()) {
            $data =Banner::select('banners.*','users.name as users_name')->join('users', 'users.id', '=', 'banners.added_by');
            $status = $request->get('status');
            $keywords = $request->get('search')['value'];
            $datatables =  Datatables::of($data)
                ->filter(function ($query) use ($keywords, $status) {
                    if ($status) {
                        return $query->where('banners.status', 'like', $status);
                    }
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        return $query->where('banners.title', 'like', "%{$keywords}%")->orWhere('users.name', 'like', "%{$keywords}%")->orWhere('banners.description', 'like', "%{$keywords}%")->orWhere('banners.tag_line', 'like', "%{$keywords}%")->orWhereDate("banners.created_at", $date);
                    }
                })
                ->addIndexColumn()
               
                ->addColumn('status', function ($row) {
                    $status = '<a href="javascript:void(0);" class="badge badge-light-'.(($row->status == 'published') ? 'success': 'danger').'" tooltip="Click to '.(($row->status == 'published') ? 'Unpublish' : 'Publish').'" onclick="return commonChangeStatus(' . $row->id . ',\''.(($row->status == 'published') ? 'unpublished': 'published').'\', \'banner\')">'.ucfirst($row->status).'</a>';
                    return $status;
                })
                ->editColumn('banner_image', function ($row) {
                    if ($row->banner_image) {
                        
                        $bannerImagePath = 'banner/'.$row->id.'/main_banner/'.$row->banner_image;
                        $url = Storage::url($bannerImagePath);
                        $path = asset($url);
                        $banner_image = '<div class="symbol symbol-45px me-5"><img src="' . $path . '" alt="" /><div>';
                    } else {
                        $path = asset('userImage/no_Image.jpg');
                        $banner_image = '<div class="symbol symbol-45px me-5"><img src="' . $path . '" alt="" /><div>';
                    }
                    return $banner_image;
                })
                ->editColumn('other_image', function ($row) {
                    if ($row->banner_image) {
                        $bannerImagePath = 'banner/'.$row->id.'/other_banner/'.$row->banner_image;
                        $url = Storage::url($bannerImagePath);
                        $path = asset($url);
                        $banner_image = '<div class="symbol symbol-45px me-5"><img src="' . $path . '" alt="" /><div>';
                    } else {
                        $path = asset('userImage/no_Image.jpg');
                        $banner_image = '<div class="symbol symbol-45px me-5"><img src="' . $path . '" alt="" /><div>';
                    }
                    return $banner_image;
                })
               
                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })

                ->addColumn('action', function ($row) {
                    $edit_btn = '<a href="javascript:void(0);" onclick="return  openForm(\'banner\',' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                    <i class="fa fa-edit"></i>
                </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'banner\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';

                    return $edit_btn . $del_btn;
                })
                ->rawColumns(['action', 'status', 'banner_image','other_image']);
            return $datatables->make(true);
        }
        $breadCrum  = array( 'Banner');
        $title      = 'Banner';
        return view('platform.banner.index', compact('breadCrum', 'title'));
    }
    public function modalAddEdit(Request $request)
    {

        $id                 = $request->id;
        $from               = $request->from;
        $info               = '';
        $modal_title        = 'Add Banner';
        if (isset($id) && !empty($id)) {
            $info           = Banner::find($id);
            $modal_title    = 'Update Banner';
        }
        
        return view('platform.banner.add_edit_modal', compact('info', 'modal_title', 'from'));
        
    }
    public function saveForm(Request $request,$id = null)
    {
        $id             = $request->id;
        $validator      = Validator::make($request->all(), [
                                'title' => 'required|string|unique:banners,title,' . $id . ',id,deleted_at,NULL',
                                'avatar' => 'mimes:jpeg,png,jpg',

                            ]);
        $banner_id       = '';

        if ($validator->passes()) {
                                 
            if ($request->image_remove_logo == "yes") {
                $ins['brand_logo'] = '';
            }
 
            $ins['title']               = $request->title;
            $ins['description']         = $request->description;
            $ins['tag_line']            = $request->tag_line;
            $ins['order_by']            = $request->order_by ?? 0;
            $ins['added_by']            = Auth::id();
            if($request->status == "1")
            {
                $ins['status']          = 'published';
            } else {
                $ins['status']          = 'unpublished';
            }
            $error                  = 0;
            $info                   = Banner::updateOrCreate(['id' => $id], $ins);
            $banner_id               = $info->id;

            if ($request->hasFile('avatar')) {
                
                $directory = 'banner/'.$banner_id;
                Storage::deleteDirectory('public/'.$directory);

                $file                   = $request->file('avatar');
                $imageName              = uniqid().$file->getClientOriginalName();
                if (!is_dir(storage_path("app/public/banner/".$banner_id."/main_banner"))) {
                    mkdir(storage_path("app/public/banner/".$banner_id."/main_banner"), 0775, true);
                }
                if (!is_dir(storage_path("app/public/banner/".$banner_id."/other_banner"))) {
                    mkdir(storage_path("app/public/banner/".$banner_id."/other_banner"), 0775, true);
                }
                $mainBanner            = 'public/banner/'.$banner_id ."/main_banner/" .$imageName;
                Image::make($file)->resize(1600,475)->save(storage_path('app/' . $mainBanner));

                $otherBanner            = 'public/banner/'.$banner_id ."/other_banner/". $imageName;
                Image::make($file)->resize(1600,420)->save(storage_path('app/' . $otherBanner));
                $info->banner_image       = $imageName;
                $info->update();
            }
            $message                    = (isset($id) && !empty($id)) ? 'Updated Successfully' : 'Added successfully';
        } else {
            $error                      = 1;
            $message                    = $validator->errors()->all();
        }
        return response()->json(['error' => $error, 'message' => $message, 'banner_id' => $banner_id]);
    }
    public function changeStatus(Request $request)
    {
        $id             = $request->id;
        $status         = $request->status;
        $info           = Banner::find($id);
        $info->status   = $status;
        $info->update();
        return response()->json(['message'=>"You changed the Banner status!",'status'=>1]);

    }
    public function delete(Request $request)
    {
        $id         = $request->id;
        $info       = Banner::find($id);
        $info->delete();
        return response()->json(['message'=>"Successfully deleted Banner!",'status'=>1]);
    }
    public function export()
    {
        return Excel::download(new BannerExport, 'banner.xlsx');
    }

    public function exportPdf()
    {
        $list       = Banner::select('banners.*','users.name as users_name')->join('users', 'users.id', '=', 'banners.added_by')->get();
        $pdf        = PDF::loadView('platform.exports.banner.excel', array('list' => $list, 'from' => 'pdf'))->setPaper('a4', 'landscape');
        return $pdf->download('banner.pdf');
    }
}
