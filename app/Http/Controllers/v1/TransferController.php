<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transfer;
use App\Models\Wallet;
use App\Models\TransactionHistory;
use Validator;  
use App\User;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class TransferController extends Controller
{
    public function SendMoneyProcess(Request $request)
    {
        $user           = Auth::user();//session
        $getuser        = Auth::id();
        $email_receiver = $request->email_receiver;
        //$money          =encrypt("30") ;
        $validator = Validator::make($request->all(), [
            'amount_send'    =>'required', 
            'email_receiver' =>'required|email|min:2|max:225',
            'currency'       =>'required|digits:1', 
           
        ]);

        //check if the all the camp is filled
        if ($validator->fails()) {
            return response()->json($validator->errors(), 401);
        }


        //check if email_receiver exist
       if(!$check_email_receiver= User::where('email',$request->email_receiver)->first() )
       {
        return response()->json([
            'message' => 'this user not exist in our database',
        
          ], 403);
       }
      
        /***********************************************************/
        /*******************   GET USER STATUS/ROLE   **************/
        /***********************************************************/
      
        $GetUserStatus = User::where('id',$getuser)->value('status');
        $GetUserRole   = User::where('id',$getuser)->value('user_role_id');

        //STATUS VERIFICATION
        if($GetUserStatus == env("STATUS"))
        {
            return response()->json([
                'message' => 'This user is not active! Account Blocked',
            
              ], 403);
        }

        
        if($GetUserRole == env("MERCHANT_ID"))
        {
            return response()->json([
                'message' => 'This user do not have the permission to do this action.',
            
              ], 403);
        }

        /***********************************************************/
        /*******************  FINISH GET USER STATUS  **************/
        /***********************************************************/



        /***********************************************************/
        /***************  WALLET CHECKING/VERIFICATION  ************/
        /***********************************************************/
        $ClientBalance     = Wallet::where('user_id',$getuser)->first();//making a select to get the current amount for a checking
        $GetClientIdentity = User::where('id',$getuser)->first();
        $ClientEmail       = $GetClientIdentity->email;
        $ClientBalance     = decrypt($ClientBalance->balance);
        $ClientId          = $getuser;//user logged id
        $Deposit           = $request->input('amount_send');//what the client just deposit in the merchant account
        $EmailReceiver     = $request->input('email_receiver');//Merchant account
        $Substraction      = $ClientBalance - $Deposit;//soustraction du balance actuel
       

       //SEARCH FOR THE MERCHANT IDENTITY                           
        $MerchantIdentity  = User::Where('email',$email_receiver)->first(); 
        $MerchantName      = $MerchantIdentity->name;
        $MerchantLastname  = $MerchantIdentity->lastname; 
        $MerchantId        = $MerchantIdentity->id;


        //Create an Order Number
        $Characters        = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $Hash              = mt_rand(1000000, 9999999). mt_rand(1000000, 9999999). $Characters[rand(0, strlen($Characters) - 1)];
        $OrderNumber       = str_shuffle($Hash);


       //CHECKING MERCHANT CURRENT BALANCE
       $MerchantID         = User::Where('email',$email_receiver)->value('id');
       $MerchantBalance    = Wallet::Where('user_id',$MerchantID)->value('balance');
       $MerchantBalance    = decrypt($MerchantBalance);//Get the current balance
       $MoneyAddedInAccount= $Deposit + $MerchantBalance; 
      

       //USER CAN'T SEND MONEY ON HIS OWN ACCOUNT
        if($ClientEmail === $email_receiver ){
            return response()->json([
                'message' => 'You cannot make a transfer on your own account!',
                
            ], 401);
        }

       // return $he=encrypt("20");
        /***********************************************************/
        /***************     STARTING MONEY TRANSFER    ************/
        /***********************************************************/
        if($Deposit == env("BALANCE_TO_NOTHING"))
        {
            return response()->json([
                'message' => 'You balance is 0,00. You need to recharge your account!',
                
            ], 401);
        }
      

        //verify the amount before to make the transfert
        else if( ($ClientBalance >= $Deposit) && ($Deposit != env("BALANCE_TO_NOTHING")))
        {
            //*************************************************************************** */
             //******************CONSULTAR UM SERVIÇO AUTORIZADO EXTERNO***************** */
            //*************************************************************************** */
                $client = new \GuzzleHttp\Client(); 
                $body = [
                    'message' => "mock",
                ];
        
            
                $response = $client->request(
                    'POST',
                    'https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6',
                    [
                        'headers' => [
                            'content-type'  => 'application/json',
                            'accept'        => 'application/ld+json',
                        ],
                        'body' => json_encode($body),
                    ]
                );

            //*************************************************************************** */
             //******************  FIM DA CONSULTAR AUTORIZADO EXTERNO  ***************** */
            //*************************************************************************** */
                if ($response->getStatusCode()== 200) {
                    Transfer::create([
                        'amount'                => $Deposit,
                        'order_number'          => $OrderNumber,
                        'ip_address'            => $request->ip(),
                        'currency_id'           => $request->currency,
                        'sender_id'             => $getuser,
                        'receiver_id'           => $MerchantId,
                    ]);
        
                    //UPDATING THE ACCOUNT BALANCE AFTER TRANSFER
                    Wallet::where('user_id', $getuser)->update(['balance' => encrypt($Substraction)]);
                    Wallet::where('user_id', $MerchantId)->update(['balance' => encrypt($MoneyAddedInAccount)]);
        
                        //INSERT TO HISTORY 
                        $shipping=   TransactionHistory::create([
                            'order_number'           => $OrderNumber,
                            'amount'                 => $Deposit,
                            'currency_id'            => $request->currency,
                            'user_id'                => $getuser,
                            'user_origin_id'         => $getuser,
                            'user_destiny_id'        => $MerchantId,
                            
                        ]);
        
                        $SaveReceiverId = $shipping->replicate()->fill([
                            'user_id'          => $MerchantId 
                        ]);
        
                                    
                        $SaveReceiverId->save();
        
                                
                        /***********************************************************/
                        /**********************GET THE SENDER IDENTITY**************/
                        /***********************************************************/
                        $GetCompleteName = User::Where('id', $getuser)->first(); 
        
                        $name_sender     = $GetCompleteName->name;
                        $lastname_sender = $GetCompleteName->last_name;
                        $email_sender    = $GetCompleteName->email;
        
                        /***********************************************************/
                        /***************************FINISH**************************/
                        /***********************************************************/
        
        
                        /***********************************************************/
                        /*************************SENDING EMAIL*********************/
                        /***********************************************************/
                            $mail_date = now();
                            try
                            {      
                                //RECEIVER
                                Mail::send('mail.transfer_receiver', [ 'name' => $MerchantName,'lastname'=>$MerchantLastname ,'amount_history'=> $Deposit, 'name_sender' => $name_sender,'mail_date'=>$mail_date], function ($message) use ($EmailReceiver){  
                                    $message->to($EmailReceiver);
                                    $message->subject('Dépôt Reçu');
                        
                                });
                                
                                //SENDER
                                Mail::send('mail.transfer_sender', [ 'name_sender' => $name_sender,'mail_date'=>$mail_date,'lastname_sender'=>$lastname_sender ,'amount_history'=> $Deposit, 'name' => $MerchantName,'lastname'=>$MerchantLastname], function ($message) use ($email_sender){  
                                    $message->to($email_sender);
                                    $message->subject('Transferência Autorisada');
                        
                                });
    
                                return response()->json([
                                    'message' => 'Notification sent',
                                    "success" => true,
                                ], 201);
        
                            }
                
                            catch (Exception $e)
                            {
                                return response()->json([
                                    'message' => 'Not authorized!',
                                ], 401);
                                
                            }
                    }
                    
                    else
                    {
                        return response()->json([
                            'message' => "Não foi autorizado",
                        ], 401);
                    }

            
        }
        else
        {
            return response()->json([
                'message' => " Your balance is low and can't support this transaction!",
            ], 401);
        }
                    
        
       
    }
}
