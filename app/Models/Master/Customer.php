<?php

namespace App\Models\Master; 

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens,HasFactory,SoftDeletes;

    protected $guarded = ['id'];

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
