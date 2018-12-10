<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LoanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // dd($this->loan);
        return [
            'id' => $this->id,
            'user' => $this->user,
            'amount' => $this->amount,
            'duration' => $this->duration,
            'repayment_freq' => $this->repayment_freq,
            'interest_rate' => $this->interest_rate,
            'arr_fee' => $this->arr_fee,
            'status' => $this->status,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
            'amount_left' => $this->amountLeft(),
            'repayments' => $this->repayment
        ];
    }
}
