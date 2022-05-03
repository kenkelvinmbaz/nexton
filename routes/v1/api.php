<?php
  /************************************************************************************/
  /*************************      AUTH USER/CLIENT CONTROLLER    *********************/
  /***********************************************************************************/
  
  //Register a user or Signin
  Route::post('signin', 'v1\AuthController@Signin'); 
  //Login to the system and verify the authentication
  Route::post('login', 'v1\AuthController@Login');
   //GET TOKEN FROM EMAIL TO CONFIRM THE USER EMAIL
  Route::get('welcome/{token}', 'v1\AuthController@TokenVerificationSecurity');

  //SEND TOKEN TO THE USER EMAIL
  Route::post('confirm/account', 'v1\AuthController@ConfirmAccount');

/***********************************************************************************/
/*************************       MERCHANT CONTROLLER       *************************/
/***********************************************************************************/
  Route::post('merchant/signin','v1\MerchantController@Signin');
  Route::post('merchant/login','v1\MerchantController@Login');

/***********************************************************************************/
/********************************SECURE ROUTE*************************************/
/***********************************************************************************/
 Route::group(['middleware' => ['apiSecure']], function(){

        //LogOut 
        Route::post('logout', 'v1\AuthController@logout');
   

/***********************************************************************************/
/*********************************   Balance Controller *****************************/
/***********************************************************************************/
  // See for Balance
  Route::get('balance', 'v1\WalletController@SeeBalance');


/***********************************************************************************/
/*************************       TRANSFER CONTROLLER       *************************/
/***********************************************************************************/
Route::post('transfer','v1\TransferController@SendMoneyProcess'); 



});