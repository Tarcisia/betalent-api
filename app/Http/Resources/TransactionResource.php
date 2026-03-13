<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'external_id' => $this->external_id,
            'status' => $this->status,
            'refund_status' => $this->refund_status,
            'amount' => $this->amount,
            'card_last_numbers' => $this->card_last_numbers,
            'client' => [
                'id' => $this->client?->id,
                'name' => $this->client?->name,
                'email' => $this->client?->email,
            ],
            'gateway' => [
                'id' => $this->gateway?->id,
                'name' => $this->gateway?->name,
                'slug' => $this->gateway?->slug,
            ],
            'created_at' => $this->created_at,
        ];
    }
}