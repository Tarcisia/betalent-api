<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionDetailResource extends JsonResource
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
            'products' => $this->products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'amount' => $product->amount,
                    'quantity' => $product->pivot->quantity,
                    'unit_amount' => $product->pivot->unit_amount,
                    'total_amount' => $product->pivot->total_amount,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
