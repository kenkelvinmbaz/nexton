<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $table = "wallet";

    protected $fillable = [
        'balance', 'hide_balance','currency_id','user_id','updated_at','created_at'
    ];
}
