<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Loan;
use App\Repayment;
use App\Http\Resources\RepaymentResource;

class RepaymentController extends Controller
{
    public function store(Request $request, Loan $loan){
        // check if current authenticated user is the owner of the loan
        if ($request->user()->id !== $loan->user_id){
            return response()->json(['message' => 'You can only update your own loans.'], 403);
        }
        if ($loan->status == 'Accepted'){
            $repay = Repayment::create(
                [
                    'user_id' => $request->user()->id,
                    'loan_id' => $loan->id,
                    'amount' => $request->amount,
                ]
            );
            if ($loan->repayment()->sum('amount') >= $loan->calcLoan()){
                // update status to complete
                $loan->status = 'Completed';
                $loan->save();
            }
            return new RepaymentResource($repay);
        }
        return response()->json(['message' => 'Your loan status is not Accepted.'], 403);
    }

    public function __construct(){
        $this->middleware('auth:api');
    }
}
