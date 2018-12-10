<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RepaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'user_id' => $this->user_id,
            'loan_id' => $this->loan_id,
            'amount' => $this->amount,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
            'loan' => $this->loan,
        ];
    }
}
