<?php

namespace App\Services\Payment\Gateways;

use Illuminate\Support\Facades\Http;
use App\Services\Payment\Contracts\PaymentGatewayInterface;

class Gateway2Service implements PaymentGatewayInterface
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.gateway2.url');
    }

    public function name(): string
    {
        return 'gateway_2';
    }

    public function charge(array $payload): array
    {
        $response = Http::withHeaders([
            'Gateway-Auth-Token' => 'tk_f2198cc671b5289fa856',
            'Gateway-Auth-Secret' => '3d15e8ed6131446ea7e3456728b1211f',
        ])->post(
            $this->baseUrl . '/transacoes',
            [
                'valor' => $payload['amount'],
                'nome' => $payload['name'],
                'email' => $payload['email'],
                'numeroCartao' => $payload['card_number'],
                'cvv' => $payload['cvv']
            ]
        );

        return $response->json();
    }

    public function refund(string $externalId): array
    {
        $response = Http::withHeaders([
            'Gateway-Auth-Token' => 'tk_f2198cc671b5289fa856',
            'Gateway-Auth-Secret' => '3d15e8ed6131446ea7e3456728b1211f',
        ])->post(
            $this->baseUrl . '/transacoes/reembolso',
            [
                'id' => $externalId
            ]
        );

        return $response->json();
    }
}