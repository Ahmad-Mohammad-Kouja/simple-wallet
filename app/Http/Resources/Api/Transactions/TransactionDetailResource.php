<?php

namespace App\Http\Resources\Api\Transactions;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'amount' => $this->amount,
            'user' => $this->when(
                $this->user_id !== null,
                fn() => [
                    'id' => $this->user_id,
                    'name' => $this->user_name,
                ]
            )
        ];
    }
}
