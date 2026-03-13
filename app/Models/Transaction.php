<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'client_id',
        'gateway_id',
        'external_id',
        'status',
        'refund_status',
        'amount',
        'card_last_numbers',
        'gateway_response'
    ];

    protected $casts = [
        'gateway_response' => 'array'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function gateway()
    {
        return $this->belongsTo(Gateway::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'transaction_products')
            ->withPivot('quantity', 'unit_amount', 'total_amount')
            ->withTimestamps();
    }
}
