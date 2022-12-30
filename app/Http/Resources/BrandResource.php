<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BrandResource extends JsonResource
{
   
    public function toArray($request)
    {

        $brandLogoPath          = 'brands/'.$this->id.'/option1/'.$this->brand_logo;

        if(!Storage::exists( $brandLogoPath)) {
            $path               = asset('userImage/no_Image.jpg');
        } else {
            $url                    = Storage::url($brandLogoPath);
            $path                   = asset($url);
        }

        

        $tmp[ 'id' ]            = $this->id;
        $tmp[ 'title' ]         = $this->brand_name;
        $tmp[ 'slug' ]          = $this->slug;
        $tmp[ 'image' ]         = $path;
        $tmp[ 'brand_banner' ]  = $this->brand_banner;
        $tmp[ 'description' ]   = $this->short_description;
        $tmp[ 'notes' ]         = $this->notes;

        return $tmp;
        
    }
}
