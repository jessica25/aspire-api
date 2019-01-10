<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    /*
        test to register with email and password
        expected return 200 
    */
    public function testRegisterWithEmailPass()
    {
        $payload = [
            'name' => 'John1',
            'email' => 'john1@example.com',
            'password' => bcrypt('secret'),
        ];

        $this->json('POST', '/api/register', $payload)
            ->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in',
        ]);
    }

    /*
        test to register without email and password
        expected return 422 
    */
    public function testsRequiresPasswordEmailAndName()
    {
        $this->json('POST', '/api/register')
            ->assertStatus(422)
            ->assertJson([
                'errors' =>
                [
                    'name' => ['Name is required!'],
                    'email' => ['Email is required!'],
                    'password' => ['Password is required!'],
                ]
            ]);
    }
}
