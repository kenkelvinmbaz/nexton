<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttempPin extends Model
{
    protected $table = "attempt_pin";
     
    protected $fillable = [
         'mac_address','ip_address','auth_type_id','user_id','created_at','updated_at'
     ];
}
