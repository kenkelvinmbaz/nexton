<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\User;
use Session;

class LoginTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    
   /** @test */
    public function a_visitor_can_able_to_login()
    {
        $user = factory(User::class)->create([
            'password' => bcrypt($password = '12345678'),
        ]);

        $response = $this->post('api/login', [
            'email' => $user->email,
            'password' =>  $password,
        ]);

       $response->assertStatus(200);

     
    }

    /** @test */
    public function user_cannot_login_with_incorrect_password()
    {
        Session::start();

        $credential = [
            'email' => 'myah51@example.com',
            'password' => 'incorrectpass'
        ];
    
        $response = $this->post('api/login',$credential);
    
        $response->assertStatus(401);
    }


    /** @test */
    public function user_cannot_login_with_empty_password_and_empty_email()
    {

        $credential = [
            'email' => '',
            'password' => ''
        ];
    
        $response = $this->post('api/login',$credential);
    
        $response->assertStatus(401);
    }


 

    // /** @test */
    // public function user_must_inform_email_before_to_make_transfer()
    // {

    //     $this->assertEquals($errors->get('name')[0],"your custom error message");// only for checking exact message
    // }

   
  
}
