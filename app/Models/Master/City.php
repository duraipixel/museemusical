<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Master\State;
class City extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'city',
        'country_id',
        'state_id',
        'pincode_id',
        'description',
        'added_by',
        'status'
    ];
    public function state()
    {
        return $this->belongsTo(State::class,'state_id','id');
    }
}
