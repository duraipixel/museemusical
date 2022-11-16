<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'mobile_no',
        'customer_no',
        'email_verified_at',
        'password',
        'remember_token',
        'dob',
        'profile_image',
        'address',
        'status',
    ];
}
