<?php

namespace App\Services\Payment\Contracts;

interface PaymentGatewayInterface
{
    public function charge(array $payload): array;

    public function refund(string $externalId): array;

    public function name(): string;
}