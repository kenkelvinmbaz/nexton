<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\UserRole;
use App\Models\Country;
use App\Models\Merchant;
use App\Models\TransactionHistory;


class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    // public $timestamps = false;
    protected $table = "user";
    protected $fillable = [
        'name','last_name', 'email', 'password','phone_number','cpf','pin','user_role_id','identity_cardFace','identity_cardBack','status','ip_address','country_id'
    ];

    public function UserRole()
    {
        return $this->belongsTo(UserRole::class,'user_role_id','id');
    }

    public function Country()
    {
        return $this->belongsTo(Country::class,'country_id','id');
    }
    public function Merchant()
    {
        return $this->hasOne(Merchant::class,'user_id','id');
    }

    public function TransactionHistory()
    {
        return $this->hasOne(TransactionHistory::class,'user_id','id');
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

     /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

}
