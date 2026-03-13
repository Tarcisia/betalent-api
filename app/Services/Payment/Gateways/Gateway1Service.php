<?php

namespace App\Services\Payment\Gateways;

use Illuminate\Support\Facades\Http;
use App\Services\Payment\Contracts\PaymentGatewayInterface;

class Gateway1Service implements PaymentGatewayInterface
{
    private string $baseUrl;

    public function __construct()
    {   
        $this->baseUrl = config('services.gateway1.url');
    }
    
    public function name(): string
    {
        return 'gateway_1';
    }

    private function authenticate(): string
    {
        $response = Http::post($this->baseUrl . '/login', [
            'email' => 'dev@betalent.tech',
            'token' => 'FEC9BB078BF338F464F96B48089EB498'
        ]);

        return $response->json('token');
    }

    public function charge(array $payload): array
    {
        $token = $this->authenticate();

        $response = Http::withToken($token)->post(
            $this->baseUrl . '/transactions',
            [
                'amount' => $payload['amount'],
                'name' => $payload['name'],
                'email' => $payload['email'],
                'cardNumber' => $payload['card_number'],
                'cvv' => $payload['cvv']
            ]
        );

        return $response->json();
    }

    public function refund(string $externalId): array
    {
        $token = $this->authenticate();

        $response = Http::withToken($token)
            ->post($this->baseUrl . "/transactions/$externalId/charge_back");

        return $response->json();
    }
}