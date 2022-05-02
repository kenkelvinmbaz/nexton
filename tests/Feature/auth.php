<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class auth extends TestCase
{

    public function test_signin_field_not_empty()
    {
        $this->withoutExceptionHandling();
        
        $response= $this->post('v1/signin',[
            'name'         => 'Leonardo',
            'last_name'    => 'Di caprio',
            'email'        => 'leonardo@gmail.com'
        ]);

        $response->assertOk();


       $this->assertStatus(200);
    }
}
