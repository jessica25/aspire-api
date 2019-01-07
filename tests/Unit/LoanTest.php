<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Loan;
use App\User;
use App\Http\Resources\LoanResource;

class LoanTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

    /*
        test to create loan without authentication
        expected return 401 with message Unauthenticated.
    */
    public function testCreateLoanWithoutAuth()
    {
        $loan = factory(Loan::class)->make();
        $response = $this->json('POST','/api/loans', $loan->toArray());
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    /*
        test to create loan with authentication
        expected return 200 with status true and message Loan created
    */
    public function testCreateLoanWithAuth()
    {
        $loan = factory(Loan::class)->make();
        $user = factory(User::class)->create();
        $response = $this->actingAs($user, 'api')->json('POST','/api/loans', $loan->toArray());
        $response->assertStatus(200);
        $response->assertJson(['status' => true]);
        $response->assertJson(['message' => 'Loan created']);
        $response->assertJson(['data' => $loan->toArray()]);
        $response->assertJson(['data' => ['user' => $user->toArray()]]);
    }
    /*
        test to get all loan for this user without authentication
        expected return 401 with message Unauthenticated.
    */
    public function testGetAllLoanWithoutAuth()
    {
        $response = $this->json('GET','/api/loans');
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    /*
        test to get all loan for this user with authentication
        expected return 200 with collection of Loan owned by this user
    */
    public function testGetAllLoanWithAuth()
    {
        $user = factory(User::class)->create();
        $response = $this->actingAs($user, 'api')->json('GET','/api/loans');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
            '*' => [
                'id', 
                'user', 
                'amount', 
                'duration', 
                'repayment_freq', 
                'interest_rate', 
                'arr_fee', 
                'status', 
                'created_at', 
                'updated_at', 
                'amount_left', 
                'repayments', 
                ]
            ]
        ]);
    }

    /*
        test to get all loan with admin user
        expected return 200 with collection of all Loan
    */
    public function testGetAllLoanWithAdmin()
    {

    }

    /*
        test to get one loan for this user without authentication
        expected return 401 with message Unauthenticated.
    */
    public function testGetOneLoanWithoutAuth()
    {
        $loan = factory(Loan::class)->make();
        $response = $this->json('GET', '/api/loans/'.$loan->id);
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    /*
        test to get one exist loan for this user with authentication
        expected return 200 with one loan data
    */
    public function testGetOneExistLoanWithAuth()
    {
        $user = factory(User::class)->create();
        $loan = factory(Loan::class)->create([
            'user_id' => $user->id,
        ]);
        $response = $this->actingAs($user, 'api')->json('GET', '/api/loans/'.$loan->id);
        $response->assertStatus(200);
        // assert the user
        $response->assertJson(['data' => ['user' => $user->toArray()]]);
        // exclude user_id in $loan
        unset($loan{'user_id'});
        $response->assertJson(['data' => $loan->toArray()]);
    }

    /*
        test to get one non exist loan for this user with authentication
        expected return 404 with message No query results for model [App\\Loan].
    */
    public function testGetOneNonExistLoanWithAuth()
    {
        $user = factory(User::class)->create();
        $response = $this->actingAs($user, 'api')->json('GET', '/api/loans/0');
        $response->assertStatus(404);
        $response->assertJson(['message' => 'No query results for model [App\\Loan].']);
    }

    /*
        test to update one exist loan for this user without authentication
        expected return 401 with message Unauthenticated.
    */
    public function testUpdateLoanWithoutAuth()
    {
        $response = $this->json('PATCH', '/api/loans/0');
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    /*
        test to update one exist loan for this user with authentication
        expected return 200 with message Unauthenticated.
    */
    public function testUpdateLoanWithAuth()
    {
        $user = factory(User::class)->create();
        $loan = factory(Loan::class)->create([
            'user_id' => $user->id,
        ]);
        $response = $this->actingAs($user, 'api')->json('PATCH', '/api/loans/'.$loan->id);
        $response->assertStatus(200);
        // assert the user
        $response->assertJson(['data' => ['user' => $user->toArray()]]);
        // exclude user_id in $loan
        unset($loan{'user_id'});
        $response->assertJson(['data' => $loan->toArray()]);
    }

    /*
        test to update one exist loan owned by another user with authentication
        expected return 403 with error You can only edit your own loans.
    */
    public function testUpdateOtherLoanWithAuth()
    {
        $response = $this->json('PATCH', '/api/loans/0');
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }
}
