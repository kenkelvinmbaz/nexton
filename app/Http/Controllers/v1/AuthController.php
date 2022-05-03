<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\User;
use Carbon\Carbon;
use Auth;
use App\Models\Wallet;
use App\Models\Role;
use App\Models\WelcomePassword;
use App\Models\AttempAuth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;  
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use LaravelLegends\PtBrValidator\Rules\Cpf;
use Illuminate\Support\Facades\Hash;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Http;


class AuthController extends Controller
{
    /*****************************************/
    /**********  Login in the App      *******/
    /*****************************************/
  
    public function Login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);
        $email       = $request->email;
        $status      = User::where('email',$email)->value('status');

        /*****************************************/
        /********** VALIDATION STEP SECURITY ******/
        /*****************************************/
        $validator = Validator::make($request->all(), [
            'email'       => 'required|string|email|max:255',
            'password'    => 'required',
         
        ]);

         //STEP 1
         if($validator->fails()){
            return response()->json([ "message" => $validator->errors(),], 401);
        }

        //Verify if the aaccunt is active
        if($status=='inactive')
        {
            return response()->json(['error' => 'This user is not actived'], 403);
        }
        if($status=='blocked')
        {
            return response()->json(['error' => 'This user is blocked'], 403);
        }

        if (!$token = auth('api')->attempt($credentials)) {
           

            $user_id      = User::where('email',$email)->value('id');
            $attemps      = AttempAuth::where(['user_id'=>$user_id,'auth_type_id'=>2, 'created_at'=> Carbon::today()])->get();
            $attemps      = $attemps->count();
            $OneAttemps   = 1;
            //Check attemps to login

            if($attemps == $OneAttemps)
            {
                AttempAuth::create([
                    "ip_address"    => $request->ip(),
                    "auth_type_id"  => 2,
                    "user_id"       => $user_id,
                    "created_at"    => now(),
                ]);

                return response()->json(['error' => 'Unauthorized'], 401);
            }
            if($attemps <1)
            {
                AttempAuth::create([
                    "ip_address"    => $request->ip(),
                    "auth_type_id"  => 2,
                    "user_id"       => $user_id,
                    "created_at"    => now(),
                 ]);

                 return response()->json(['error' => 'Unauthorized'], 401);
            }
           
            else
            {
                User::where('email',$email)->update(['status' => "blocked" ]);
                $getIdentity    = User::where('email',$email)->first();
                $name           = $getIdentity->name;
                $last_name      = $getIdentity->last_name;
                $RECIPIENTS     = $getIdentity->phone_number;
                $RECIPIENTS     = '+'.$getIdentity->phone_number;
                $mail_date      = now();
                try{

                    //E-MAIL D'INTRUSION DE SÉCURITÉ
                    Mail::send('mail.AccountBlocked', [ 'name' => $name, 'lastname' => $last_name,'mail_date'=>$mail_date], function ($message) use ($email){  
                        $message->to($email);
                        $message->subject('Notificação de segurança');
            
                    });

                    $Phonemessage="".$name." "."".$last_name."\r\n Uma pessoa não identificada tentou acessar sua conta. Considerando esta ação suspeita, sua conta foi bloqueada. Se você acha que isso é apenas um erro de sua parte, entre em contato com nosso serviço enviando um e-mail para support@nextonpagamento.com ou no whatsapp: (55)41992643666".''."\r\Time Nexton";
                    $TWILIO_SID        = env("TWILIO_SID");
                    $TWILIO_AUTH_TOKEN = env("TWILIO_AUTH_TOKEN");
                    $TWILIO_NUMBER     = env("TWILIO_NUMBER");
                    $client = new Client($TWILIO_SID, $TWILIO_AUTH_TOKEN);
                    $client->messages->create(
                        $RECIPIENTS,
                        [
                            "body" => $Phonemessage,
                            "from" => $TWILIO_NUMBER
                        ]
                    );
        
                    return response()->json(['error' => 'Unauthorized',"sucess" =>false,"message"=>"account blocked"], 403);
                }
                catch (Exception $e)
                {
                    return response()->json([
                        'message' => 'You dont have access! E-mail not send',
                    ], 401);
                    
                } 
            }

           
        }
      

        try{
           
            $getIdentity    = User::where('email',$email)->first();
            $name           = $getIdentity->name;
            $last_name      = $getIdentity->last_name;
           
            Mail::send('mail.name', [ 'name' => $name], function ($message) use ($email){  
                $message->to($email);
                $message->subject('Alerta de segurança');
    
            });

            return $this->respondWithToken($token);
        }
        catch (Exception $e)
        {
            return response()->json([
                'message' => 'You dont have access! E-mail not send',
            ], 401);
            
        } 
        
            return $this->respondWithToken($token);
            
            
    }

    /*****************************************/
    /******** Return the Token created  *****/
    /*****************************************/

    protected function RespondWithToken($token)
    {
     
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 9,
        ]);
    }
 
    /*****************************************/
    /**********    Create a new User   *******/
    /*****************************************/

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
                    'user_role_id'  => 1,
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
       
   
    


    /*****************************************/
    /**********  TO know who is logged *******/
    /*****************************************/
    public function Me(Request $request)
    {
        
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        $request->session()->put('user', $user['id']);
        $getsession = $request->session()->get('user');

        return response()->json(auth()->user());
    }

   /*****************************************/
    /**********       Logout          *******/
    /*****************************************/
    public function Logout()
    {
        auth('api')->logout();
        
        return response()->json(['message' => 'Successfully logged out']);
    }

  
    public function Refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    
    public function TokenVerificationSecurity($token)
    {
       
            //VERIFICANDO SE O TOKEN RECEBIDO E IGUAL AO O QUE ESTÁ NA BASE
            $data = WelcomePassword::where('token',$token)->first();
            
            //verify if there is data
            if(empty($data))
            {
                return view('mail.Erroforgotpassword');
            }
            else
            {
                $email = $data->email;
                return view('mail.confirmAccount',compact('email'));
            }

    }

    public function ConfirmAccount(Request $request)
    {
            //check validation
            $validator = Validator::make($request->all(), [
                'email'=> 'required|string',
            ]);
    
            //check if the all the camp is filled
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'you have to fill all informations',
                ], 402);
            }
         
         $email    = $request->email;  

         $update=  User::where('email',$email)->update(['status' => "active"]);
         return response()->json([
            'message' => 'Account actived',
        ], 201);
    }


}
