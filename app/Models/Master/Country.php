<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'nice_name',
        'iso',
        'iso3',
        'num_code',
        'phone_code',
        'status'
    ];
}
