<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use App\Services\Payment\Gateways\Gateway1Service;
use App\Services\Payment\Gateways\Gateway2Service;

class RefundService
{
    public function execute(Transaction $transaction): Transaction
    {
        $transaction->load('gateway');

        $service = match ($transaction->gateway?->slug) {
            'gateway_1' => new Gateway1Service(),
            'gateway_2' => new Gateway2Service(),
            default => null,
        };

        if (!$service) {
            throw new \RuntimeException('Gateway da transação não suportado.');
        }

        $response = $service->refund($transaction->external_id);

        $refundSucceeded = ($response['status'] ?? null) === 'refunded';

        DB::transaction(function () use ($transaction, $refundSucceeded, $response) {
            $transaction->update([
                'status' => $refundSucceeded ? 'refunded' : $transaction->status,
                'refund_status' => $refundSucceeded ? 'processed' : 'failed',
                'gateway_response' => $response,
            ]);
        });

        return $transaction->fresh(['client', 'gateway', 'products']);
    }
}