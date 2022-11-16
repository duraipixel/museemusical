<?php

namespace App\Models\Master;

use App\Models\Category\SubCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerAddress extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'customer_id',
        'address_type_id',
        'name',
        'email',
        'mobile_no',
        'address_line1',
        'address_line2',
        'landmark',
        'city_id',
        'state_id',
        'country_id',
        'post_code_id',
        'is_default',
    ];
    
    public function country()
    {
        return $this->hasOne(Country::class, 'id', 'country_id');
    }
    public function state()
    {
        return $this->hasOne(State::class, 'id', 'state_id');
    }
    public function city()
    {
        return $this->hasOne(City::class, 'id', 'city_id');
    }
    public function pincode()
    {
        return $this->hasOne(Pincode::class, 'id', 'post_code_id');
    }
    public function subCategory()
    {
        return $this->hasOne(SubCategory::class, 'id', 'address_type_id');
    }
}
