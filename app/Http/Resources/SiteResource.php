<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class SiteResource extends JsonResource
{
    
    public function toArray($request)
    {
        
        $childTmp = [];
        $tmp[ 'site_name' ]         = $this->site_name;
        $tmp[ 'site_email' ]        = $this->site_email;
        $tmp[ 'site_mobile_no' ]    = $this->site_mobile_no;
        $tmp[ 'favicon' ]           = asset('/') . $this->favicon;
        $tmp[ 'logo' ]              = asset('/') . $this->logo;
        $tmp[ 'address' ]           = $this->address;
        $tmp[ 'copyrights' ]        = $this->copyrights;
        $tmp[ 'is_tax_inclusive' ]  = $this->is_tax_inclusive;
        $tmp[ 'payment_mode' ]      = $this->payment_mode;

        if( isset( $this->links ) && !empty( $this->links ) ) {
            foreach ($this->links as $child ) {
                $innerTmp['link_name']      = $child->link_name;
                $innerTmp['link_icon']      = $child->link_icon;
                $innerTmp['link_url']       = $child->link_url;
                $childTmp[]         = $innerTmp;
            }
        }
        $tmp['links']       = $childTmp;

        return $tmp;
    }
}
