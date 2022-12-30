<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brands extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'brand_name',
        'brand_logo',
        'slug',
        'brand_banner',
        'short_description',
        'notes',
        'order_by',
        'added_by',
        'status'
    ];
}
