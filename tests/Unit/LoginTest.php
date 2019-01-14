<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;

class LoginTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

    /*
        test to login without email and password
        expected return 401 with error Unauthorized
    */
    public function testLoginWithoutEmailPass()
    {
        $response = $this->json('POST','/api/login');
        $response->assertStatus(401);
        $response->assertJson(['error' => 'Unauthorized']);
    }

    /*
        test to login without password
        expected return 401 with error Unauthorized
    */
    public function testLoginWithoutPass()
    {
        $payload = ['email' => 'john@example.com'];
        $response = $this->json('POST','/api/login', $payload);
        $response->assertStatus(401);
        $response->assertJson(['error' => 'Unauthorized']);
    }

    /*
        test to login with email and password
        expected return 200 
    */
    public function testLoginWithEmailPass()
    {
        $user = factory(User::class)->create([
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => bcrypt('secret'),
        ]);

        $payload = ['email' => 'john@example.com', 'password' => 'secret'];

        $this->json('POST', '/api/login', $payload)
            ->assertStatus(200)
            ->assertJsonStructure([
                    'access_token',
                    'token_type',
                    'expires_in',
            ]);
    }
}
