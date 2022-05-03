<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\User;

class WalletController extends Controller
{
    /****************************************************************/
    /******************SHOW BALANCE FROM WALLET***********************/
    /****************************************************************/
    public function SeeBalance()
    {
        $user    = Auth::user();//session
        $getuser = Auth::id();

       $balance =  Wallet::where('user_id',$getuser)->first('balance');

       if(empty($balance))
       {
            return response()->json([
                'message' => 'no balance found',
            
            ], 401);

       }

       $balance = $balance->balance;
      
       $balance= number_format((float)decrypt($balance), 2, '.', '');

       return $this->ReturnBalance($balance);     
      
    }


    /************************************************************/
    /**************  GET THE BALANCE PROTECTED  *****************/
    /************************************************************/
    protected function ReturnBalance($balance)
    {
        return response()->json($balance, 201);

    }

}
