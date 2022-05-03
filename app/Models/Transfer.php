<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $table = "transfer";
    
    protected $fillable = [
        'amount', 'order_number','ip_address','sender_id','receiver_id','currency_id','created_at','updated_at'
    ];
}
