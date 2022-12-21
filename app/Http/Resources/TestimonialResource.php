<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TestimonialResource extends JsonResource
{
    
    public function toArray($request)
    {

        $tmp[ 'id' ]                    = $this->id;
        $tmp[ 'title' ]                 = $this->title;
        $tmp[ 'image' ]                 = asset($this->image);
        $tmp[ 'short_description' ]     = $this->short_description;
        $tmp[ 'long_description' ]      = $this->long_description;

        return $tmp;
        
    }

}
