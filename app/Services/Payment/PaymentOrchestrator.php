<?php

namespace App\Services\Payment;

use App\Models\Gateway;
use App\Services\Payment\Gateways\Gateway1Service;
use App\Services\Payment\Gateways\Gateway2Service;

class PaymentOrchestrator 
{
    public function charge(array $payload)
    {
        $gateways = Gateway::query()
            ->where('is_active', true)
            ->orderBy('priority')
            ->get();

        foreach ($gateways as $gateway) {

            $service = match ($gateway->slug) {
                'gateway_1' => new Gateway1Service(),
                'gateway_2' => new Gateway2Service(),
                default => null
            };

            if (!$service) {
                continue;
            }

            try {
                $response = $service->charge($payload);

                if (!empty($response['id'])) {
                    return [
                        'success' => true,
                        'gateway' => $gateway,
                        'external_id' => $response['id'],
                        'response' => $response
                    ];
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        return [
            'success' => false
        ];
    }
}