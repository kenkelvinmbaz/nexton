<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Validator;  
use App\Models\AttempAuth;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Mail;
use App\Models\TransactionHistory;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class SecurityController extends Controller
{
    /****************************************************************/
     /*********************EDIT USER PASSWORD************************/
    /****************************************************************/
    public function UpdatePassword(Request $request)
    {
        $user    = Auth::user();//session
        $getuser = Auth::id();
        $hash    = bcrypt($request->password);//hash

        //verify if the user send the passsword
        if(empty($request->password))
        {
            return response()->json([
                'message' => "can't let empty. Fill the information!",
            
            ], 421);
        }

        $validator = Validator::make($request->all(), [
            'password'=> 'required|string|min:8', 
           
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

       

            $checker  = User::where('id',$getuser)->first(); 
            $email    = $checker->email;
            $name     = $checker->name;
            $lastname = $checker->last_name;

            //Verify if the hash matched before updated users data
            if(!Hash::check($request->password,  $checker->password)){

                $balance = User::where('id', $getuser)->update(['password' => $hash]);
                
                try{

                    Mail::send('mail.PasswordUpdated', [ 'name' => $name,'lastname'=> $lastname], function ($message) use ($email){  
                        $message->to($email);
                        $message->subject('Actualisation Mot de passe');
            
                    });

                    return response()->json([
                        'message' => 'okay,  Password updated!',
                    
                    ], 201);
                    
                }
                catch (Exception $e)
                    {
                        return response()->json([
                            'message' => 'You dont have access! E-mail not send',
                        ], 401);
                        
                    }

            }else{

                return response()->json([
                    'message' => 'This password already exist! Create a new one please.',
                
                ], 400);
            }
    }

    /****************************************************************/
     /***********************EDIT USER PIN ***************************/
    /****************************************************************/
    public function UpdatePIN(Request $request)
    {
        $user    = Auth::user();//session
        $getuser = Auth::id();
        $hash    = bcrypt($request->pin_hash);//hash

        //verify if the user send the passsword
        if(empty($request->pin_hash))
        {
            return response()->json([
                'message' => "can't let empty. Fill the information!",
            
            ], 421);
        }

        $validator = Validator::make($request->all(), [
            'pin_hash'=> 'required|digits:4', 
           
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

       

       $checker  = User::where('id',$getuser)->first(); 
       $email    = $checker->email;
       $name     = $checker->name;
       $lastname = $checker->last_name;

            //Verify if the hash matched before updated users data
            if(!Hash::check($request->pin_hash,  $checker->pin_hash)){

                $balance = User::where('id', $getuser)->update(['pin_hash' => $hash]);


                try{

                    Mail::send('mail.PinUpdated', [ 'name' => $name,'lastname'=> $lastname], function ($message) use ($email){  
                        $message->to($email);
                        $message->subject('Actualisation PIN');
            
                    });

                    return response()->json([
                        'message' => 'okay,  PIN updated!',
                    
                    ], 201);
                    
                }
                catch (Exception $e)
                    {
                        return response()->json([
                            'message' => 'You dont have access! E-mail not send',
                        ], 401);
                        
                    } 
                
               

            }else{

                return response()->json([
                    'message' => 'This PIN already exist! Create a new one please.',
                
                ], 400);
            }
    }

    /****************************************************************/
     /*****************VERIFY PASSOWRD REGISTERED*******************/
    /****************************************************************/
    public function VerifyPassword(Request $request)
    {
        $user    = Auth::user();//session
        $getuser = Auth::id();
        $validator = Validator::make($request->all(), [
            'password'=> 'required|string|min:8', 
           
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $hash     = bcrypt($request->password);//hash

        $checker  = User::where('id',$getuser)->first(); 
 
            //Verify if the hash matched before updated users data
            if(!Hash::check($request->password,  $checker->password)){
                return response()->json([
                    'message' => 'Your password is wrong',
                
                ], 400);
            }else{
            
                return response()->json([
                    'message' => 'everything is okay',
                
                ], 200);
            }
    }

    /****************************************************************/
     /***********************VERIFY PIN ACCOUNT***********************/
    /****************************************************************/
    public function VerifyPIN(Request $request)
    {
        $user    = Auth::user();//session
        $getuser = Auth::id();

        $validator = Validator::make($request->all(), [
            'pin_hash'=> 'required|digits:4', 
           
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        $hash    = bcrypt($request->pin_hash);//hash

       $checker  = User::where('id',$getuser)->first(); 

            //Verify if the hash matched before updated users data
            if(!Hash::check($request->pin_hash,  $checker->pin_hash)){

                $attemps      = AttempAuth::where(['user_id'=>$getuser,'auth_type_id'=>3, 'created_at'=> Carbon::today()])->get();
                $attemps      = $attemps->count();
                $OneAttemps   = 1;
                
                if($attemps == $OneAttemps)
                {
                    AttempAuth::create([
                        "ip_address"    => $request->ip(),
                        "mac_address"   => substr(exec('getmac'), 0, 17),
                        "auth_type_id"  => 3,
                        "user_id"       => $getuser,
                        "created_at"    => now(),
                    ]);

                    return response()->json(['error' => 'Unauthorized'], 409);
                }
                if($attemps <1)
                {
                    AttempAuth::create([
                        "ip_address"    => $request->ip(),
                        "mac_address"   => substr(exec('getmac'), 0, 17),
                        "auth_type_id"  => 3,
                        "user_id"       => $getuser,
                        "created_at"    => now(),
                    ]);

                    return response()->json([
                        'message' => 'Your pin is wrong',
                    
                    ], 400);
                }
         
                else
                {
                    User::where('id', $getuser)->update(['status' => "blocked" ]);
                    $getIdentity    = User::where('id',$getuser)->first();
                    $name           = $getIdentity->name;
                    $last_name      = $getIdentity->last_name;
                    $email          = $getIdentity->email;
                    $RECIPIENTS     = '+'.$getIdentity->phone_number;
                    $mail_date      = now();

                    try{

                        
                    //E-MAIL D'INTRUSION DE SÉCURITÉ
                    Mail::send('mail.AccountBlocked', [ 'name' => $name, 'lastname' => $last_name,'mail_date'=>$mail_date], function ($message) use ($email){  
                        $message->to($email);
                        $message->subject('Intrusion de sécurité');
            
                    });

                    $Phonemessage="".$name." "."".$last_name."\r\n Une personne non identifiée a essayé d'effectuer un achat avec votre compte. Considerant cette action suspecte, votre compte a été bloqué. Si vous pensez qu'il s'agit juste d'une erreur de votre part, veuillez contacter notre service en envoyant un mail vers support@nuvenspay.com ou sur whatsApp: (243)825514851".''."\r\nÉquipe NuvensPay";
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
            
                    return response()->json(['error' => 'Unauthorized',"sucess" =>false,"message"=>"account blocked"], 401);
                    }
                    catch (Exception $e)
                    {
                        return response()->json([
                            'message' => 'You dont have access! E-mail not send',
                        ], 401);
                        
                    } 
            }
                
            }else{
            
                return response()->json([
                    'message' => 'everything is okay',
                
                ], 200);
            }
    }

    /****************************************************************/
     /***********************BLOCK ACCOUNT***************************/
    /****************************************************************/
    public function BlockAccount(Request $request)
    {
       $user    = Auth::user();//session
       $getuser = Auth::id();

       $status = $request->input('status');
      

       $GetCurrentStatus= User::where('id',$getuser)->value('status');

     
        if($status == $GetCurrentStatus)
        {
            User::where('id', $getuser)->update(['status' => "inactive" ]);

            return response()->json([
            'message' => 'status updated!',
        
            ], 201);
        }
        
        if($status != $GetCurrentStatus)
        {
            User::where('id', $getuser)->update(['status' => "active" ]);
            
            return response()->json([
            'message' => 'status updated!',
        
            ], 201);
        }
              
    }


}
