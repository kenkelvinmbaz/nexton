<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TransactionHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class HistoryController extends Controller
{
    public function NuvensPayHistoryLimitFive()
    {
        $user    = Auth::user();//session
        $user_id = Auth::id();
    
        $history = TransactionHistory::with(['TransactionType'])->where('user_id',$user_id)->orderBy('id','DESC')->get();
        
       // TransactionHistory::where('user_id', $user_id)->get();

          //CHECK IF THE CURRENT USER EXIST IN DATABASE
          if (empty($history)) 
          {
              return response()->json([
                  'message' => 'no history found.',
              ], 401);
          }

          return response()->json($history, 201);
    }
    
    public function TransactionMadeToday()
    {
        $user    = Auth::user();//session
        $user_id = Auth::id();
        $EmailSession  = $user->email;
       
        $year    = date('Y');

     return   $post =  DB::select(DB::raw(
        "SELECT
            t00.movement_type_id as movement_type_id, 
            t09.id as UserId, t09.name as UserName, t09.last_name as UserLastName, t09.email UserEmail,
            t00.amount as Amount, t00.order_number as OrderNumber, t01.Name as TransactionType, t02.Name as MovementType, 
            t03.Name as RechargeMethod, t04.Name as PaymentMethod, -- t05.Name as TransferenceMethod,
            t06.Name as PurchaseType,
            t06.Name as WithdrawalMethod, -- t08.Name as Currency, 
            t00.created_at as TransactionDate,
            (SELECT name FROM user WHERE id = t00.user_origin_id) as UserOriginName,
            (SELECT last_name FROM user WHERE id = t00.user_origin_id) as UserOriginLastName,
            (SELECT email FROM user WHERE id = t00.user_origin_id) as UserOriginEmail,
            (SELECT name FROM user Where id = t00.user_destiny_id) as UserDestinyName,
            (SELECT last_name FROM user WHERE id = t00.user_destiny_id) AS UserDestinyLastName,
            (Select email FROM user WHERE id = t00.user_destiny_id) AS UserDestinyEmail,
            (SELECT product_name FROM tappay_product JOIN tappay_payment ON tappay_product.merchant_id = tappay_payment.merchant_Id WHERE tappay_payment.order_number = t00.order_number group by  t00.order_number) AS ProductName, 
            (Select giftcard_type.name FROM giftcard_type LEFT JOIN giftcard ON giftcard_type.id = giftcard.giftcard_type_id
            LEFT JOIN giftcard_purchase ON giftcard_purchase.GiftCard_id = giftcard.id
            WHERE giftcard_purchase.order_number = t00.order_number) AS GiftCardTypeName
            FROM transaction_history AS t00
            INNER JOIN transaction_type t01
            ON t01.id = t00.transaction_type_id
            INNER JOIN movement_type t02
            On t02.id = t00.movement_type_id
            INNER JOIN recharge_method t03
            On t03.id = t00.recharge_method_id
            INNER JOIN payment_method t04
            On t04.id = t00.payment_method_id
            -- Inner Join transference_method t05
            -- On t05.id = t00.transference_method_id
            INNER JOIN purchase_type t06
            On t06.id = t00.purchase_type_id
            Inner JOIN withdrawal_method t07
            On t07.id = t00.withdrawal_method_id
            -- Inner Join currency t08
            -- On t08.id = t00.currency_id
            Inner Join user as t09
            On t09.id = t00.user_id
            LEFT JOIN tappay_product as t10
            ON t10.merchant_id = t00.user_id
            Where t00.user_id = $user_id and   (DATE(t00.created_at) = DATE(NOW()) and  year(t00.created_at) = $year)
             group by t00.order_number order by t00.id desc
        ")) ;
    }

    public function jan()
    {
        $user    = Auth::user();//session
        $user_id = Auth::id();
        $month   = '01';
        $EmailSession  = $user->email;
        $year    = date('Y');

     return   $post =  DB::select(DB::raw(
        "SELECT
        t00.movement_type_id as movement_type_id, 
        t09.id as UserId, t09.name as UserName, t09.last_name as UserLastName, t09.email UserEmail,
        t00.amount as Amount, t00.order_number as OrderNumber, t01.Name as TransactionType, t02.Name as MovementType, 
        t03.Name as RechargeMethod, t04.Name as PaymentMethod, -- t05.Name as TransferenceMethod,
        t06.Name as PurchaseType,
        t06.Name as WithdrawalMethod, -- t08.Name as Currency, 
        t00.created_at as TransactionDate,
        (SELECT name FROM user WHERE id = t00.user_origin_id) as UserOriginName,
        (SELECT last_name FROM user WHERE id = t00.user_origin_id) as UserOriginLastName,
        (SELECT email FROM user WHERE id = t00.user_origin_id) as UserOriginEmail,
        (SELECT name FROM user Where id = t00.user_destiny_id) as UserDestinyName,
        (SELECT last_name FROM user WHERE id = t00.user_destiny_id) AS UserDestinyLastName,
        (Select email FROM user WHERE id = t00.user_destiny_id) AS UserDestinyEmail,
        (SELECT product_name FROM tappay_product JOIN tappay_payment ON tappay_product.merchant_id = tappay_payment.merchant_Id WHERE tappay_payment.order_number = t00.order_number group by  t00.order_number) AS ProductName, 
        (Select giftcard_type.name FROM giftcard_type LEFT JOIN giftcard ON giftcard_type.id = giftcard.giftcard_type_id
        LEFT JOIN giftcard_purchase ON giftcard_purchase.GiftCard_id = giftcard.id
        WHERE giftcard_purchase.order_number = t00.order_number) AS GiftCardTypeName
        FROM transaction_history AS t00
        INNER JOIN transaction_type t01
        ON t01.id = t00.transaction_type_id
        INNER JOIN movement_type t02
        On t02.id = t00.movement_type_id
        INNER JOIN recharge_method t03
        On t03.id = t00.recharge_method_id
        INNER JOIN payment_method t04
        On t04.id = t00.payment_method_id
        -- Inner Join transference_method t05
        -- On t05.id = t00.transference_method_id
        INNER JOIN purchase_type t06
        On t06.id = t00.purchase_type_id
        Inner JOIN withdrawal_method t07
        On t07.id = t00.withdrawal_method_id
        -- Inner Join currency t08
        -- On t08.id = t00.currency_id
        Inner Join user as t09
        On t09.id = t00.user_id
        LEFT JOIN tappay_product as t10
        ON t10.merchant_id = t00.user_id
        Where t00.user_id = $user_id and  (month(t00.created_at) = $month and  year(t00.created_at) = $year)
         group by t00.order_number order by t00.id desc
    ")) ;
    }

    public function fev()
    {
        $user    = Auth::user();//session
        $user_id = Auth::id();
        $month   = '02';
        $EmailSession  = $user->email;
        $year    = date('Y');

     return   $post =  DB::select(DB::raw(
        "SELECT
        t00.movement_type_id as movement_type_id, 
        t09.id as UserId, t09.name as UserName, t09.last_name as UserLastName, t09.email UserEmail,
        t00.amount as Amount, t00.order_number as OrderNumber, t01.Name as TransactionType, t02.Name as MovementType, 
        t03.Name as RechargeMethod, t04.Name as PaymentMethod, -- t05.Name as TransferenceMethod,
        t06.Name as PurchaseType,
        t06.Name as WithdrawalMethod, -- t08.Name as Currency, 
        t00.created_at as TransactionDate,
        (SELECT name FROM user WHERE id = t00.user_origin_id) as UserOriginName,
        (SELECT last_name FROM user WHERE id = t00.user_origin_id) as UserOriginLastName,
        (SELECT email FROM user WHERE id = t00.user_origin_id) as UserOriginEmail,
        (SELECT name FROM user Where id = t00.user_destiny_id) as UserDestinyName,
        (SELECT last_name FROM user WHERE id = t00.user_destiny_id) AS UserDestinyLastName,
        (Select email FROM user WHERE id = t00.user_destiny_id) AS UserDestinyEmail,
        (SELECT product_name FROM tappay_product JOIN tappay_payment ON tappay_product.merchant_id = tappay_payment.merchant_Id WHERE tappay_payment.order_number = t00.order_number group by  t00.order_number) AS ProductName, 
        (Select giftcard_type.name FROM giftcard_type LEFT JOIN giftcard ON giftcard_type.id = giftcard.giftcard_type_id
        LEFT JOIN giftcard_purchase ON giftcard_purchase.GiftCard_id = giftcard.id
        WHERE giftcard_purchase.order_number = t00.order_number) AS GiftCardTypeName
        FROM transaction_history AS t00
        INNER JOIN transaction_type t01
        ON t01.id = t00.transaction_type_id
        INNER JOIN movement_type t02
        On t02.id = t00.movement_type_id
        INNER JOIN recharge_method t03
        On t03.id = t00.recharge_method_id
        INNER JOIN payment_method t04
        On t04.id = t00.payment_method_id
        -- Inner Join transference_method t05
        -- On t05.id = t00.transference_method_id
        INNER JOIN purchase_type t06
        On t06.id = t00.purchase_type_id
        Inner JOIN withdrawal_method t07
        On t07.id = t00.withdrawal_method_id
        -- Inner Join currency t08
        -- On t08.id = t00.currency_id
        Inner Join user as t09
        On t09.id = t00.user_id
        LEFT JOIN tappay_product as t10
        ON t10.merchant_id = t00.user_id
        Where t00.user_id = $user_id and  (month(t00.created_at) = $month and  year(t00.created_at) = $year)
         group by t00.order_number order by t00.id desc
    ")) ;
    }

    public function mar()
    {
        $user    = Auth::user();//session
        $user_id = Auth::id();
        $month   = '03';
        $EmailSession  = $user->email;
        $year    = date('Y');

     return   $post =  DB::select(DB::raw(
        "SELECT
        t00.movement_type_id as movement_type_id, 
        t09.id as UserId, t09.name as UserName, t09.last_name as UserLastName, t09.email UserEmail,
        t00.amount as Amount, t00.order_number as OrderNumber, t01.Name as TransactionType, t02.Name as MovementType, 
        t03.Name as RechargeMethod, t04.Name as PaymentMethod, -- t05.Name as TransferenceMethod,
        t06.Name as PurchaseType,
        t06.Name as WithdrawalMethod, -- t08.Name as Currency, 
        t00.created_at as TransactionDate,
        (SELECT name FROM user WHERE id = t00.user_origin_id) as UserOriginName,
        (SELECT last_name FROM user WHERE id = t00.user_origin_id) as UserOriginLastName,
        (SELECT email FROM user WHERE id = t00.user_origin_id) as UserOriginEmail,
        (SELECT name FROM user Where id = t00.user_destiny_id) as UserDestinyName,
        (SELECT last_name FROM user WHERE id = t00.user_destiny_id) AS UserDestinyLastName,
        (Select email FROM user WHERE id = t00.user_destiny_id) AS UserDestinyEmail,
        (SELECT product_name FROM tappay_product JOIN tappay_payment ON tappay_product.merchant_id = tappay_payment.merchant_Id WHERE tappay_payment.order_number = t00.order_number group by  t00.order_number) AS ProductName, 
        (Select giftcard_type.name FROM giftcard_type LEFT JOIN giftcard ON giftcard_type.id = giftcard.giftcard_type_id
        LEFT JOIN giftcard_purchase ON giftcard_purchase.GiftCard_id = giftcard.id
        WHERE giftcard_purchase.order_number = t00.order_number) AS GiftCardTypeName
        FROM transaction_history AS t00
        INNER JOIN transaction_type t01
        ON t01.id = t00.transaction_type_id
        INNER JOIN movement_type t02
        On t02.id = t00.movement_type_id
        INNER JOIN recharge_method t03
        On t03.id = t00.recharge_method_id
        INNER JOIN payment_method t04
        On t04.id = t00.payment_method_id
        -- Inner Join transference_method t05
        -- On t05.id = t00.transference_method_id
        INNER JOIN purchase_type t06
        On t06.id = t00.purchase_type_id
        Inner JOIN withdrawal_method t07
        On t07.id = t00.withdrawal_method_id
        -- Inner Join currency t08
        -- On t08.id = t00.currency_id
        Inner Join user as t09
        On t09.id = t00.user_id
        LEFT JOIN tappay_product as t10
        ON t10.merchant_id = t00.user_id
        Where t00.user_id = $user_id and  (month(t00.created_at) = $month and  year(t00.created_at) = $year)
         group by t00.order_number order by t00.id desc
    ")) ;
    }

    public function avr()
    {
        $user    = Auth::user();//session
        $user_id = Auth::id();
        $month   = '04';
        $EmailSession  = $user->email;
        $year    = date('Y');

     return   $post =  DB::select(DB::raw(
        "SELECT
        t00.movement_type_id as movement_type_id, 
        t09.id as UserId, t09.name as UserName, t09.last_name as UserLastName, t09.email UserEmail,
        t00.amount as Amount, t00.order_number as OrderNumber, t01.Name as TransactionType, t02.Name as MovementType, 
        t03.Name as RechargeMethod, t04.Name as PaymentMethod, -- t05.Name as TransferenceMethod,
        t06.Name as PurchaseType,
        t06.Name as WithdrawalMethod, -- t08.Name as Currency, 
        t00.created_at as TransactionDate,
        (SELECT name FROM user WHERE id = t00.user_origin_id) as UserOriginName,
        (SELECT last_name FROM user WHERE id = t00.user_origin_id) as UserOriginLastName,
        (SELECT email FROM user WHERE id = t00.user_origin_id) as UserOriginEmail,
        (SELECT name FROM user Where id = t00.user_destiny_id) as UserDestinyName,
        (SELECT last_name FROM user WHERE id = t00.user_destiny_id) AS UserDestinyLastName,
        (Select email FROM user WHERE id = t00.user_destiny_id) AS UserDestinyEmail,
        (SELECT product_name FROM tappay_product JOIN tappay_payment ON tappay_product.merchant_id = tappay_payment.merchant_Id WHERE tappay_payment.order_number = t00.order_number group by  t00.order_number) AS ProductName, 
        (Select giftcard_type.name FROM giftcard_type LEFT JOIN giftcard ON giftcard_type.id = giftcard.giftcard_type_id
        LEFT JOIN giftcard_purchase ON giftcard_purchase.GiftCard_id = giftcard.id
        WHERE giftcard_purchase.order_number = t00.order_number) AS GiftCardTypeName
        FROM transaction_history AS t00
        INNER JOIN transaction_type t01
        ON t01.id = t00.transaction_type_id
        INNER JOIN movement_type t02
        On t02.id = t00.movement_type_id
        INNER JOIN recharge_method t03
        On t03.id = t00.recharge_method_id
        INNER JOIN payment_method t04
        On t04.id = t00.payment_method_id
        -- Inner Join transference_method t05
        -- On t05.id = t00.transference_method_id
        INNER JOIN purchase_type t06
        On t06.id = t00.purchase_type_id
        Inner JOIN withdrawal_method t07
        On t07.id = t00.withdrawal_method_id
        -- Inner Join currency t08
        -- On t08.id = t00.currency_id
        Inner Join user as t09
        On t09.id = t00.user_id
        LEFT JOIN tappay_product as t10
        ON t10.merchant_id = t00.user_id
        Where t00.user_id = $user_id and  (month(t00.created_at) = $month and  year(t00.created_at) = $year)
         group by t00.order_number order by t00.id desc
    ")) ;
    }

    public function mai()
    {
        $user    = Auth::user();//session
        $user_id = Auth::id();
        $EmailSession  = $user->email;
        $month   = '05';
        $year    = date('Y');

     return   $post =  DB::select(DB::raw(
        "SELECT
        t00.movement_type_id as movement_type_id, 
        t09.id as UserId, t09.name as UserName, t09.last_name as UserLastName, t09.email UserEmail,
        t00.amount as Amount, t00.order_number as OrderNumber, t01.Name as TransactionType, t02.Name as MovementType, 
        t03.Name as RechargeMethod, t04.Name as PaymentMethod, -- t05.Name as TransferenceMethod,
        t06.Name as PurchaseType,
        t06.Name as WithdrawalMethod, -- t08.Name as Currency, 
        t00.created_at as TransactionDate,
        (SELECT name FROM user WHERE id = t00.user_origin_id) as UserOriginName,
        (SELECT last_name FROM user WHERE id = t00.user_origin_id) as UserOriginLastName,
        (SELECT email FROM user WHERE id = t00.user_origin_id) as UserOriginEmail,
        (SELECT name FROM user Where id = t00.user_destiny_id) as UserDestinyName,
        (SELECT last_name FROM user WHERE id = t00.user_destiny_id) AS UserDestinyLastName,
        (Select email FROM user WHERE id = t00.user_destiny_id) AS UserDestinyEmail,
        (SELECT product_name FROM tappay_product JOIN tappay_payment ON tappay_product.merchant_id = tappay_payment.merchant_Id WHERE tappay_payment.order_number = t00.order_number group by  t00.order_number) AS ProductName, 
        (Select giftcard_type.name FROM giftcard_type LEFT JOIN giftcard ON giftcard_type.id = giftcard.giftcard_type_id
        LEFT JOIN giftcard_purchase ON giftcard_purchase.GiftCard_id = giftcard.id
        WHERE giftcard_purchase.order_number = t00.order_number) AS GiftCardTypeName
        FROM transaction_history AS t00
        INNER JOIN transaction_type t01
        ON t01.id = t00.transaction_type_id
        INNER JOIN movement_type t02
        On t02.id = t00.movement_type_id
        INNER JOIN recharge_method t03
        On t03.id = t00.recharge_method_id
        INNER JOIN payment_method t04
        On t04.id = t00.payment_method_id
        -- Inner Join transference_method t05
        -- On t05.id = t00.transference_method_id
        INNER JOIN purchase_type t06
        On t06.id = t00.purchase_type_id
        Inner JOIN withdrawal_method t07
        On t07.id = t00.withdrawal_method_id
        -- Inner Join currency t08
        -- On t08.id = t00.currency_id
        Inner Join user as t09
        On t09.id = t00.user_id
        LEFT JOIN tappay_product as t10
        ON t10.merchant_id = t00.user_id
        Where t00.user_id = $user_id and  (month(t00.created_at) = $month and  year(t00.created_at) = $year)
         group by t00.order_number order by t00.id desc
    ")) ;
    }

    public function jui()
    {
        $user    = Auth::user();//session
        $user_id = Auth::id();
        $EmailSession  = $user->email;
        $EmailSession  = $user->email;
        $month   = 06;
        $year    = date('Y');

        // $email = DB::table('users')->where('id',$user_id)->value('email');

     return   $post = DB::select(DB::raw(
        "SELECT
        t00.movement_type_id as movement_type_id, 
        t09.id as UserId, t09.name as UserName, t09.last_name as UserLastName, t09.email UserEmail,
        t00.amount as Amount, t00.order_number as OrderNumber, t01.Name as TransactionType, t02.Name as MovementType, 
        t03.Name as RechargeMethod, t04.Name as PaymentMethod, -- t05.Name as TransferenceMethod,
        t06.Name as PurchaseType,
        t06.Name as WithdrawalMethod, -- t08.Name as Currency, 
        t00.created_at as TransactionDate,
        (SELECT name FROM user WHERE id = t00.user_origin_id) as UserOriginName,
        (SELECT last_name FROM user WHERE id = t00.user_origin_id) as UserOriginLastName,
        (SELECT email FROM user WHERE id = t00.user_origin_id) as UserOriginEmail,
        (SELECT name FROM user Where id = t00.user_destiny_id) as UserDestinyName,
        (SELECT last_name FROM user WHERE id = t00.user_destiny_id) AS UserDestinyLastName,
        (Select email FROM user WHERE id = t00.user_destiny_id) AS UserDestinyEmail,
        (SELECT product_name FROM tappay_product JOIN tappay_payment ON tappay_product.merchant_id = tappay_payment.merchant_Id WHERE tappay_payment.order_number = t00.order_number group by  t00.order_number) AS ProductName, 
        (Select giftcard_type.name FROM giftcard_type LEFT JOIN giftcard ON giftcard_type.id = giftcard.giftcard_type_id
        LEFT JOIN giftcard_purchase ON giftcard_purchase.GiftCard_id = giftcard.id
        WHERE giftcard_purchase.order_number = t00.order_number) AS GiftCardTypeName
        FROM transaction_history AS t00
        INNER JOIN transaction_type t01
        ON t01.id = t00.transaction_type_id
        INNER JOIN movement_type t02
        On t02.id = t00.movement_type_id
        INNER JOIN recharge_method t03
        On t03.id = t00.recharge_method_id
        INNER JOIN payment_method t04
        On t04.id = t00.payment_method_id
        -- Inner Join transference_method t05
        -- On t05.id = t00.transference_method_id
        INNER JOIN purchase_type t06
        On t06.id = t00.purchase_type_id
        Inner JOIN withdrawal_method t07
        On t07.id = t00.withdrawal_method_id
        -- Inner Join currency t08
        -- On t08.id = t00.currency_id
        Inner Join user as t09
        On t09.id = t00.user_id
        LEFT JOIN tappay_product as t10
        ON t10.merchant_id = t00.user_id
        Where t00.user_id = $user_id and  (month(t00.created_at) = $month and  year(t00.created_at) = $year)
         group by t00.order_number order by t00.id desc
    ")) ;
    }

    public function jul()
    {
        $user    = Auth::user();//session
        $user_id = Auth::id();
        $EmailSession  = $user->email;
        $month   = '07';
        $year    = date('Y');

     return   $post = DB::select(DB::raw(
        "SELECT
        t00.movement_type_id as movement_type_id, 
        t09.id as UserId, t09.name as UserName, t09.last_name as UserLastName, t09.email UserEmail,
        t00.amount as Amount, t00.order_number as OrderNumber, t01.Name as TransactionType, t02.Name as MovementType, 
        t03.Name as RechargeMethod, t04.Name as PaymentMethod, -- t05.Name as TransferenceMethod,
        t06.Name as PurchaseType,
        t06.Name as WithdrawalMethod, -- t08.Name as Currency, 
        t00.created_at as TransactionDate,
        (SELECT name FROM user WHERE id = t00.user_origin_id) as UserOriginName,
        (SELECT last_name FROM user WHERE id = t00.user_origin_id) as UserOriginLastName,
        (SELECT email FROM user WHERE id = t00.user_origin_id) as UserOriginEmail,
        (SELECT name FROM user Where id = t00.user_destiny_id) as UserDestinyName,
        (SELECT last_name FROM user WHERE id = t00.user_destiny_id) AS UserDestinyLastName,
        (Select email FROM user WHERE id = t00.user_destiny_id) AS UserDestinyEmail,
        (SELECT product_name FROM tappay_product JOIN tappay_payment ON tappay_product.merchant_id = tappay_payment.merchant_Id WHERE tappay_payment.order_number = t00.order_number group by  t00.order_number) AS ProductName, 
        (Select giftcard_type.name FROM giftcard_type LEFT JOIN giftcard ON giftcard_type.id = giftcard.giftcard_type_id
        LEFT JOIN giftcard_purchase ON giftcard_purchase.GiftCard_id = giftcard.id
        WHERE giftcard_purchase.order_number = t00.order_number) AS GiftCardTypeName
        FROM transaction_history AS t00
        INNER JOIN transaction_type t01
        ON t01.id = t00.transaction_type_id
        INNER JOIN movement_type t02
        On t02.id = t00.movement_type_id
        INNER JOIN recharge_method t03
        On t03.id = t00.recharge_method_id
        INNER JOIN payment_method t04
        On t04.id = t00.payment_method_id
        -- Inner Join transference_method t05
        -- On t05.id = t00.transference_method_id
        INNER JOIN purchase_type t06
        On t06.id = t00.purchase_type_id
        Inner JOIN withdrawal_method t07
        On t07.id = t00.withdrawal_method_id
        -- Inner Join currency t08
        -- On t08.id = t00.currency_id
        Inner Join user as t09
        On t09.id = t00.user_id
        LEFT JOIN tappay_product as t10
        ON t10.merchant_id = t00.user_id
        Where t00.user_id = $user_id and  (month(t00.created_at) = $month and  year(t00.created_at) = $year)
         group by t00.order_number order by t00.id desc
    ")) ;
    }

    public function aou()
    {
        $user    = Auth::user();//session
        $user_id = Auth::id();
        $EmailSession  = $user->email;
        $month   = '08';
        $year    = date('Y');

     return   $post =  DB::select(DB::raw(
        "SELECT
        t00.movement_type_id as movement_type_id, 
        t09.id as UserId, t09.name as UserName, t09.last_name as UserLastName, t09.email UserEmail,
        t00.amount as Amount, t00.order_number as OrderNumber, t01.Name as TransactionType, t02.Name as MovementType, 
        t03.Name as RechargeMethod, t04.Name as PaymentMethod, -- t05.Name as TransferenceMethod,
        t06.Name as PurchaseType,
        t06.Name as WithdrawalMethod, -- t08.Name as Currency, 
        t00.created_at as TransactionDate,
        (SELECT name FROM user WHERE id = t00.user_origin_id) as UserOriginName,
        (SELECT last_name FROM user WHERE id = t00.user_origin_id) as UserOriginLastName,
        (SELECT email FROM user WHERE id = t00.user_origin_id) as UserOriginEmail,
        (SELECT name FROM user Where id = t00.user_destiny_id) as UserDestinyName,
        (SELECT last_name FROM user WHERE id = t00.user_destiny_id) AS UserDestinyLastName,
        (Select email FROM user WHERE id = t00.user_destiny_id) AS UserDestinyEmail,
        (SELECT product_name FROM tappay_product JOIN tappay_payment ON tappay_product.merchant_id = tappay_payment.merchant_Id WHERE tappay_payment.order_number = t00.order_number group by  t00.order_number) AS ProductName, 
        (Select giftcard_type.name FROM giftcard_type LEFT JOIN giftcard ON giftcard_type.id = giftcard.giftcard_type_id
        LEFT JOIN giftcard_purchase ON giftcard_purchase.GiftCard_id = giftcard.id
        WHERE giftcard_purchase.order_number = t00.order_number) AS GiftCardTypeName
        FROM transaction_history AS t00
        INNER JOIN transaction_type t01
        ON t01.id = t00.transaction_type_id
        INNER JOIN movement_type t02
        On t02.id = t00.movement_type_id
        INNER JOIN recharge_method t03
        On t03.id = t00.recharge_method_id
        INNER JOIN payment_method t04
        On t04.id = t00.payment_method_id
        -- Inner Join transference_method t05
        -- On t05.id = t00.transference_method_id
        INNER JOIN purchase_type t06
        On t06.id = t00.purchase_type_id
        Inner JOIN withdrawal_method t07
        On t07.id = t00.withdrawal_method_id
        -- Inner Join currency t08
        -- On t08.id = t00.currency_id
        Inner Join user as t09
        On t09.id = t00.user_id
        LEFT JOIN tappay_product as t10
        ON t10.merchant_id = t00.user_id
        Where t00.user_id = $user_id and  (month(t00.created_at) = $month and  year(t00.created_at) = $year)
         group by t00.order_number order by t00.id desc
    ")) ;
    }

    public function sep()
    {
        $user    = Auth::user();//session
        $user_id = Auth::id();
        $EmailSession  = $user->email;
        $month   = '09';
        $year    = date('Y');

     return   $post =  DB::select(DB::raw(
        "SELECT
        t00.movement_type_id as movement_type_id, 
        t09.id as UserId, t09.name as UserName, t09.last_name as UserLastName, t09.email UserEmail,
        t00.amount as Amount, t00.order_number as OrderNumber, t01.Name as TransactionType, t02.Name as MovementType, 
        t03.Name as RechargeMethod, t04.Name as PaymentMethod, -- t05.Name as TransferenceMethod,
        t06.Name as PurchaseType,
        t06.Name as WithdrawalMethod, -- t08.Name as Currency, 
        t00.created_at as TransactionDate,
        (SELECT name FROM user WHERE id = t00.user_origin_id) as UserOriginName,
        (SELECT last_name FROM user WHERE id = t00.user_origin_id) as UserOriginLastName,
        (SELECT email FROM user WHERE id = t00.user_origin_id) as UserOriginEmail,
        (SELECT name FROM user Where id = t00.user_destiny_id) as UserDestinyName,
        (SELECT last_name FROM user WHERE id = t00.user_destiny_id) AS UserDestinyLastName,
        (Select email FROM user WHERE id = t00.user_destiny_id) AS UserDestinyEmail,
        (SELECT product_name FROM tappay_product JOIN tappay_payment ON tappay_product.merchant_id = tappay_payment.merchant_Id WHERE tappay_payment.order_number = t00.order_number group by  t00.order_number) AS ProductName, 
        (Select giftcard_type.name FROM giftcard_type LEFT JOIN giftcard ON giftcard_type.id = giftcard.giftcard_type_id
        LEFT JOIN giftcard_purchase ON giftcard_purchase.GiftCard_id = giftcard.id
        WHERE giftcard_purchase.order_number = t00.order_number) AS GiftCardTypeName
        FROM transaction_history AS t00
        INNER JOIN transaction_type t01
        ON t01.id = t00.transaction_type_id
        INNER JOIN movement_type t02
        On t02.id = t00.movement_type_id
        INNER JOIN recharge_method t03
        On t03.id = t00.recharge_method_id
        INNER JOIN payment_method t04
        On t04.id = t00.payment_method_id
        -- Inner Join transference_method t05
        -- On t05.id = t00.transference_method_id
        INNER JOIN purchase_type t06
        On t06.id = t00.purchase_type_id
        Inner JOIN withdrawal_method t07
        On t07.id = t00.withdrawal_method_id
        -- Inner Join currency t08
        -- On t08.id = t00.currency_id
        Inner Join user as t09
        On t09.id = t00.user_id
        LEFT JOIN tappay_product as t10
        ON t10.merchant_id = t00.user_id
        Where t00.user_id = $user_id and  (month(t00.created_at) = $month and  year(t00.created_at) = $year)
         group by t00.order_number order by t00.id desc
    ")) ;
    }

    public function oct()
    {
        $user    = Auth::user();//session
        $user_id = Auth::id();
        $EmailSession  = $user->email;
        $month   = '10';
        $year    = date('Y');

     return   $post =  DB::select(DB::raw(
        "SELECT
        t00.movement_type_id as movement_type_id, 
        t09.id as UserId, t09.name as UserName, t09.last_name as UserLastName, t09.email UserEmail,
        t00.amount as Amount, t00.order_number as OrderNumber, t01.Name as TransactionType, t02.Name as MovementType, 
        t03.Name as RechargeMethod, t04.Name as PaymentMethod, -- t05.Name as TransferenceMethod,
        t06.Name as PurchaseType,
        t06.Name as WithdrawalMethod, -- t08.Name as Currency, 
        t00.created_at as TransactionDate,
        (SELECT name FROM user WHERE id = t00.user_origin_id) as UserOriginName,
        (SELECT last_name FROM user WHERE id = t00.user_origin_id) as UserOriginLastName,
        (SELECT email FROM user WHERE id = t00.user_origin_id) as UserOriginEmail,
        (SELECT name FROM user Where id = t00.user_destiny_id) as UserDestinyName,
        (SELECT last_name FROM user WHERE id = t00.user_destiny_id) AS UserDestinyLastName,
        (Select email FROM user WHERE id = t00.user_destiny_id) AS UserDestinyEmail,
        (SELECT product_name FROM tappay_product JOIN tappay_payment ON tappay_product.merchant_id = tappay_payment.merchant_Id WHERE tappay_payment.order_number = t00.order_number group by  t00.order_number) AS ProductName, 
        (Select giftcard_type.name FROM giftcard_type LEFT JOIN giftcard ON giftcard_type.id = giftcard.giftcard_type_id
        LEFT JOIN giftcard_purchase ON giftcard_purchase.GiftCard_id = giftcard.id
        WHERE giftcard_purchase.order_number = t00.order_number) AS GiftCardTypeName
        FROM transaction_history AS t00
        INNER JOIN transaction_type t01
        ON t01.id = t00.transaction_type_id
        INNER JOIN movement_type t02
        On t02.id = t00.movement_type_id
        INNER JOIN recharge_method t03
        On t03.id = t00.recharge_method_id
        INNER JOIN payment_method t04
        On t04.id = t00.payment_method_id
        -- Inner Join transference_method t05
        -- On t05.id = t00.transference_method_id
        INNER JOIN purchase_type t06
        On t06.id = t00.purchase_type_id
        Inner JOIN withdrawal_method t07
        On t07.id = t00.withdrawal_method_id
        -- Inner Join currency t08
        -- On t08.id = t00.currency_id
        Inner Join user as t09
        On t09.id = t00.user_id
        LEFT JOIN tappay_product as t10
        ON t10.merchant_id = t00.user_id
        Where t00.user_id = $user_id and  (month(t00.created_at) = $month and  year(t00.created_at) = $year)
         group by t00.order_number order by t00.id desc
    ")) ;
    }

    public function nov()
    {
        $user    = Auth::user();//session
        $user_id = Auth::id();
        $EmailSession  = $user->email;
        $month   = '11';
        $year    = date('Y');

     return   $post =  DB::select(DB::raw(
        "SELECT
            t00.movement_type_id as movement_type_id, 
            t09.id as UserId, t09.name as UserName, t09.last_name as UserLastName, t09.email UserEmail,
            t00.amount as Amount, t00.order_number as OrderNumber, t01.Name as TransactionType, t02.Name as MovementType, 
            t03.Name as RechargeMethod, t04.Name as PaymentMethod, -- t05.Name as TransferenceMethod,
            t06.Name as PurchaseType,
            t06.Name as WithdrawalMethod, -- t08.Name as Currency, 
            t00.created_at as TransactionDate,
            (SELECT name FROM user WHERE id = t00.user_origin_id) as UserOriginName,
            (SELECT last_name FROM user WHERE id = t00.user_origin_id) as UserOriginLastName,
            (SELECT email FROM user WHERE id = t00.user_origin_id) as UserOriginEmail,
            (SELECT name FROM user Where id = t00.user_destiny_id) as UserDestinyName,
            (SELECT last_name FROM user WHERE id = t00.user_destiny_id) AS UserDestinyLastName,
            (Select email FROM user WHERE id = t00.user_destiny_id) AS UserDestinyEmail,
            (SELECT product_name FROM tappay_product JOIN tappay_payment ON tappay_product.merchant_id = tappay_payment.merchant_Id WHERE tappay_payment.order_number = t00.order_number group by  t00.order_number) AS ProductName, 
            (Select giftcard_type.name FROM giftcard_type LEFT JOIN giftcard ON giftcard_type.id = giftcard.giftcard_type_id
            LEFT JOIN giftcard_purchase ON giftcard_purchase.GiftCard_id = giftcard.id
            WHERE giftcard_purchase.order_number = t00.order_number) AS GiftCardTypeName
            FROM transaction_history AS t00
            INNER JOIN transaction_type t01
            ON t01.id = t00.transaction_type_id
            INNER JOIN movement_type t02
            On t02.id = t00.movement_type_id
            INNER JOIN recharge_method t03
            On t03.id = t00.recharge_method_id
            INNER JOIN payment_method t04
            On t04.id = t00.payment_method_id
            -- Inner Join transference_method t05
            -- On t05.id = t00.transference_method_id
            INNER JOIN purchase_type t06
            On t06.id = t00.purchase_type_id
            Inner JOIN withdrawal_method t07
            On t07.id = t00.withdrawal_method_id
            -- Inner Join currency t08
            -- On t08.id = t00.currency_id
            Inner Join user as t09
            On t09.id = t00.user_id
            LEFT JOIN tappay_product as t10
            ON t10.merchant_id = t00.user_id
            Where t00.user_id = $user_id and  (month(t00.created_at) = $month and  year(t00.created_at) = $year)
             group by t00.order_number order by t00.id desc
        ")) ;
    }

    public function dec()
    {
        $user    = Auth::user();//session
        $user_id = Auth::id();
        $EmailSession  = $user->email;
        $month   = '12';
        $year    = date('Y');

     return   $post =  DB::select(DB::raw(
        "SELECT
        t00.movement_type_id as movement_type_id, 
        t09.id as UserId, t09.name as UserName, t09.last_name as UserLastName, t09.email UserEmail,
        t00.amount as Amount, t00.order_number as OrderNumber, t01.Name as TransactionType, t02.Name as MovementType, 
        t03.Name as RechargeMethod, t04.Name as PaymentMethod, -- t05.Name as TransferenceMethod,
        t06.Name as PurchaseType,
        t06.Name as WithdrawalMethod, -- t08.Name as Currency, 
        t00.created_at as TransactionDate,
        (SELECT name FROM user WHERE id = t00.user_origin_id) as UserOriginName,
        (SELECT last_name FROM user WHERE id = t00.user_origin_id) as UserOriginLastName,
        (SELECT email FROM user WHERE id = t00.user_origin_id) as UserOriginEmail,
        (SELECT name FROM user Where id = t00.user_destiny_id) as UserDestinyName,
        (SELECT last_name FROM user WHERE id = t00.user_destiny_id) AS UserDestinyLastName,
        (Select email FROM user WHERE id = t00.user_destiny_id) AS UserDestinyEmail,
        (SELECT product_name FROM tappay_product JOIN tappay_payment ON tappay_product.merchant_id = tappay_payment.merchant_Id WHERE tappay_payment.order_number = t00.order_number group by  t00.order_number) AS ProductName, 
        (Select giftcard_type.name FROM giftcard_type LEFT JOIN giftcard ON giftcard_type.id = giftcard.giftcard_type_id
        LEFT JOIN giftcard_purchase ON giftcard_purchase.giftcard_id = giftcard.id
        WHERE giftcard_purchase.order_number = t00.order_number) AS GiftCardTypeName
        FROM transaction_history AS t00
        INNER JOIN transaction_type t01
        ON t01.id = t00.transaction_type_id
        INNER JOIN movement_type t02
        On t02.id = t00.movement_type_id
        INNER JOIN recharge_method t03
        On t03.id = t00.recharge_method_id
        INNER JOIN payment_method t04
        On t04.id = t00.payment_method_id
        -- Inner Join transference_method t05
        -- On t05.id = t00.transference_method_id
        INNER JOIN purchase_type t06
        On t06.id = t00.purchase_type_id
        Inner JOIN withdrawal_method t07
        On t07.id = t00.withdrawal_method_id
        -- Inner Join currency t08
        -- On t08.id = t00.currency_id
        Inner Join user as t09
        On t09.id = t00.user_id
        LEFT JOIN tappay_product as t10
        ON t10.merchant_id = t00.user_id
        Where t00.user_id = $user_id and  (month(t00.created_at) = $month and  year(t00.created_at) = $year)
         group by t00.order_number order by t00.id desc
        ")) ;
    }

}
