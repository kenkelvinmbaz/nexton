<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;  
use App\Models\welcome_passwords;
use Illuminate\Support\Str;
use App\User;
use Carbon\Carbon;
use Auth;
use App\Models\Wallet;
use App\Models\Transfer;
use App\Models\WelcomePassword;
use App\Models\TransactionHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;


class MerchantController extends Controller
{

  /**************************************************************/
  /*****************LOGIN INTO THE APPLICATION WEB****************/
  /**************************************************************/
    public function Login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);
        
        $email       = $request->input('email');

        $status = User::where('email',$email)->value('status');
        $role   = User::where('email',$email)->value('user_role_id');

        if($status=="inactive")
        {
            return response()->json(['error' => 'This user is not actived'], 402);
        }

        if($role != env("MERCHANT_ID"))
        {
            return response()->json(['error' => 'This user is not a merchant'], 403);
        }

        if (!$token = auth('api')->claims(['email'=>$email])->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        
        try{

            $name="Caro Cliente";
            Mail::send('mail.name', [ 'name' => $name], function ($message) use ($email){  
                $message->to($email);
                $message->subject('Notificação de segurança');
    
            });

            return $this->respondWithToken($token);
        }
        catch (Exception $e)
        {
            return response()->json([
                'message' => 'You dont have access! E-mail not send',
            ], 401);
            
        } 
        
    }


  /**************************************************************/
  /*****************      CREATING TOKEN JWT     ****************/
  /**************************************************************/    
  protected function RespondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    
  /**************************************************************/
  /*****************      REGISTRATION MERCHANT    **************/
  /**************************************************************/
    public function Signin (Request $request)
    {

         /*****************************************/
        /********** VALIDATION STEP SECURITY ******/
        /*****************************************/
        $validator = Validator::make($request->all(), [
            'name'        => 'required',
            'last_name'   => 'required', 
            'phone_number'=> 'required|unique:user', 
            'email'       => 'required|string|email|max:255|unique:user',
            'pin'         => 'required',
            'password'    => 'required',
            'cpf'         => 'required|unique:user',
        ]);

        //STEP 1
        if($validator->fails()){
            return response()->json([ "message" => $validator->errors(),], 401);
        }

        //STEP 2
        $pin = Validator::make($request->all(), [
            'pin'=> 'required|digits:4',
        ]);

        //STEP 3
        $PasswordValidaton  = Validator::make($request->all(), [
            'password'=> 'required|string|min:8',
        ]);

        //STEP 4
        $NameLastNameValidation = Validator::make($request->all(), [
            'name'        => 'required|max:25|regex:/^[a-zA-ZÑñ\s]+$/',
            'last_name'   => 'required|max:25|regex:/^[a-zA-ZÑñ\s]+$/', 
        ]);

        //STEP 5 
        if ($validator->fails()) {
            return response()->json($validator->errors(), 401);
        }

        //STEP 6
        if ($pin->fails()) {
            return response()->json($pin->errors(), 401);
        }

        //STEP7 
        if ($PasswordValidaton->fails()) {
            return response()->json($PasswordValidaton->errors(), 401);
        }

        //STEP 8 
        if ($NameLastNameValidation->fails()) {
            return response()->json($NameLastNameValidation->errors(), 401);
        }

        //VERIFY IF THE USER EMAIL ALWAYS EXIST
        if (User::where('email', $request->input('email'))->exists()) {
            // user found
            return response()->json([
                'message' => 'this user already exist',
            ], 401);
         }

         else
         {
            try
            {
                $user = User::create([
                    'name'          => $request->name,
                    'last_name'     => $request->last_name,
                    'phone_number'  => $request->phone_number, 
                    'email'         => $request->email,
                    'cpf'           => $request->cpf,
                    'password'      => bcrypt($request->password),
                    'pin'           => bcrypt($request->pin_hash),
                    'user_role_id'  => 2,
                    'status'        =>'inactive',
                   
                ]);
    
                $UserId = User::orderBy('id','desc')->value('id');
    
                    if(empty($UserId))
                    {
                        return response()->json([
                            'message' => 'no id was found',
                        ], 400);
                    }
    
                $Balance = encrypt(env("BALANCE_AFTER_SIGNING"));//balance encrypt

                /***********************************************************/
                /******************  CREATE A NEW ACCOUNT    **************/
                /***********************************************************/
                                Wallet::create([
                                    'balance'     => $Balance,
                                    'user_id'     => $UserId,
                                    'currency_id' => 2,
                                    'created_at'  => now()
                                ]);
            
                                $email = $request->email;
                                $token = Str::random(10);

                /************************************************************/
                /******************  CREATE A TOKEN TO ACCESS  **************/
                /***********************************************************/
            
                                WelcomePassword::create([
                                    'email'=> $email,
                                    'token' => $token,
                                ]);

                /************************************************************/
                /****************  SENDING TOKEN TO THE EMAIL  **************/
                /***********************************************************/            
                       
                Mail::send('mail.welcome', [ 'name' => $request->name, 'lastname' => $request->last_name,'token'=> $token,], function ($message) use ($email){  
                    $message->to($email);
                    $message->subject('Bem vindo ao Nexton');
        
                });

                return response()->json([
                    'succes' => true
                ], 201);
            }
            catch(Exception $e){
                return response()->json([
                    "error" => "could not register",
                    "message" => "Unable to register user"
                ], 400);
            }
         }
    }

}
