<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Loan;
use App\User;

class RepaymentTest extends TestCase
{
    /*
        test to create one repayment for this user without authentication
        expected return 401 with message Unauthenticated.
    */
    public function testCreateRepaymentWithoutAuth()
    {
        $response = $this->json('POST', '/api/repay/0');
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    /*
        test to create one repayment for other user with authentication
        expected return 403 with message You can only update your own loans.
    */
    public function testCreateOtherRepaymentWithAuth()
    {
        $user1 = factory(User::class)->create();
        $loan1 = factory(Loan::class)->create([
            'user_id' => $user1->id,
        ]);
        $user2 = factory(User::class)->create();
        $response = $this->actingAs($user2, 'api')->json('POST', '/api/repay/'.$loan1->id);
        $response->assertStatus(403);
        $response->assertJson(['message' => 'You can only update your own loans.']);
    }

    /*
        test to create one repayment for non Accepted Loan owned by this user with authentication
        expected return 403 with message Your loan status is not Accepted.
    */
    public function testCreatePendingRepaymentWithAuth()
    {
        $user = factory(User::class)->create();
        $loan = factory(Loan::class)->create([
            'user_id' => $user->id,
            'status' => 'Pending',
        ]);
        $response = $this->actingAs($user, 'api')->json('POST', '/api/repay/'.$loan->id);
        $response->assertStatus(403);
        $response->assertJson(['message' => 'Your loan status is not Accepted.']);
    }

    /*
        test to create one repayment for Accepted Loan owned by this user with authentication
        expected return 201
    */
    public function testCreateAcceptedRepaymentWithAuth()
    {
        $user = factory(User::class)->create();
        $loan = factory(Loan::class)->create([
            'user_id' => $user->id,
            'status' => 'Accepted',
        ]);
        $payload = ['amount' => '1000'];
        $response = $this->actingAs($user, 'api')->json('POST', '/api/repay/'.$loan->id, $payload);
        $response->assertStatus(201);
        // exclude user_id in $loan
        unset($loan{'user_id'});
        $response->assertJson(['data' => ['loan' => $loan->toArray()]]);
        // assert repayment amount
        $response->assertJson(['data' => ['amount' => '1000']]);
    }

    /*
        test to create one repayment until repaid all for Accepted Loan owned by this user with authentication
        expected return 201
    */
    public function testCreateRepaymentUntilCompleteWithAuth()
    {
        $user = factory(User::class)->create();
        $loan = factory(Loan::class)->create([
            'user_id' => $user->id,
            'status' => 'Accepted',
        ]);
        $amount = ($loan['amount'] + ($loan['amount']*($loan['duration'] * $loan['interest_rate']/100)));
        $payload = ['amount' => $amount];
        $response = $this->actingAs($user, 'api')->json('POST', '/api/repay/'.$loan->id, $payload);
        $response->assertStatus(201);
        // assert the loan status is Completed
        $loan['status'] = 'Completed';
        // exclude user_id in $loan
        unset($loan{'user_id'});
        $response->assertJson(['data' => ['loan' => $loan->toArray()]]);
        // assert repayment amount
        $response->assertJson(['data' => ['amount' => $amount]]);
    }

    /*
        test to create one repayment for Completed Loan owned by this user with authentication
        expected return 403 with message Your loan status is not Accepted.
    */
    public function testCreateCompletedRepaymentWithAuth()
    {
        $user = factory(User::class)->create();
        $loan = factory(Loan::class)->create([
            'user_id' => $user->id,
            'status' => 'Completed',
        ]);
        $payload = ['amount' => '1000'];
        $response = $this->actingAs($user, 'api')->json('POST', '/api/repay/'.$loan->id, $payload);
        $response->assertStatus(403);
        $response->assertJson(['message' => 'Your loan status is not Accepted.']);
    }
}
