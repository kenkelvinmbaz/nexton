<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForgotPassWord extends Model
{
    protected $table = "forgot_password";

    protected $fillable = [
        'email', 'token','created_at','updated_at'
    ];
}
