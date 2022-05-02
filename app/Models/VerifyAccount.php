<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerifyAccount extends Model
{
    protected $table = "verify_account";

    protected $fillable = [
        'email', 'token','created_at','updated_at'
    ];
}
