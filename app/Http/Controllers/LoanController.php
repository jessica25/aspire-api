<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Loan;
use App\Http\Resources\LoanResource;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $loan = $request->user()->loans()->get();
        return LoanResource::collection($loan);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $loan = Loan::create([
            'user_id' => $request->user()->id,
            'amount' => $request->amount,
            'duration' => $request->duration,
            'repayment_freq' => $request->repayment_freq,
            'interest_rate' => $request->interest_rate,
            'arr_fee' => $request->arr_fee,
        ]);

        return new LoanResource($loan);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Loan $loan)
    {
        // check if currently authenticated user is the owner of the loan
        if ($request->user()->id !== $loan->user_id){
            return response()->json(['error' => 'You can only see your own loans.'], 403);
        }
        return new LoanResource($loan);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Loan $loan)
    {
        // check if currently authenticated user is the owner of the loan
        if ($request->user()->id !== $loan->user_id){
            return response()->json(['error' => 'You can only edit your own loans.'], 403);
        }

        if (isset($request->status)){
            $loan->status = $request->status;
        }
        if ($loan->status == 'Pending'){
            if (isset($request->amount)){
                $loan->amount = $request->amount;
            }
            if (isset($request->duration)){
                $loan->duration = $request->duration;
            }
            if (isset($request->arr_fee)){
                $loan->arr_fee = $request->arr_fee;
            }
            if (isset($request->repayment_freq)){
                $loan->repayment_freq = $request->repayment_freq;
            }
            if (isset($request->interest_rate)){
                $loan->interest_rate = $request->interest_rate;
            }
        }
        
        // $loan->update(array_filter($request->all()));
        $loan->save();

        return new LoanResource($loan);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function __construct(){
        $this->middleware('auth:api');
    }
}
