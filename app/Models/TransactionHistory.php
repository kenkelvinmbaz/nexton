<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Models\TransactionType;

class TransactionHistory extends Model
{
    protected $table = "transaction_history";

    protected $fillable = [
        'amount', 'order_number','user_id','user_origin_id','user_destiny_id','currency_id',
        'created_at','updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function TransactionType()
    {
        return $this->belongsTo(TransactionType::class);
    }
}
