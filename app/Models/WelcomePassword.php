<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WelcomePassword extends Model
{
    protected $table = "welcome_password";

    protected $fillable = [
        'email', 'token', 'created_at','updated_at'
    ];
}
