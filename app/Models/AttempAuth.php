<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttempAuth extends Model
{
    protected $table = "attempt_auth";
     
     protected $fillable = [
         'ip_address','auth_type_id','user_id','created_at','updated_at'
      ];

}
