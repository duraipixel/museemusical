<?php

namespace App\Models\Category;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubCategory extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'parent_id',
        'name',
        'description',
        'image',
        'slug',
        'order_by',
        'status',
        'added_by',
    ];
}
