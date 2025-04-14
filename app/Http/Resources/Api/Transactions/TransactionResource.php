<?php

namespace App\Http\Resources\Api\Transactions;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'number' => str($this->number)->padLeft(6, 0),
            'type' => $this->type,
            'details' => TransactionDetailResource::collection($this->whenLoaded('details')),
        ];
    }
}
