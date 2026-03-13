<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use App\Services\Payment\PaymentOrchestrator;
use App\Http\Requests\Purchase\StorePurchaseRequest;

class PurchaseController extends Controller
{
    public function __construct(private PaymentOrchestrator $paymentOrchestrator) 
    {
    }

    public function store(StorePurchaseRequest $request)
    {
        $data = $request->validated();

        $clientData = $data['client'];
        $cardData = $data['card'];
        $itemsData = $data['products'];

        $productIds = collect($itemsData)->pluck('product_id')->toArray();

        $products = Product::query()
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        $amount = 0;
        $normalizedItems = [];

        foreach ($itemsData as $item) {
            $product = $products->get($item['product_id']);

            if (!$product) {
                return response()->json([
                    'message' => 'Produto não encontrado.'
                ], 422);
            }

            $totalAmount = $product->amount * $item['quantity'];
            $amount += $totalAmount;

            $normalizedItems[] = [
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'unit_amount' => $product->amount,
                'total_amount' => $totalAmount,
            ];
        }

        $gatewayPayload = [
            'amount' => $amount,
            'name' => $clientData['name'],
            'email' => $clientData['email'],
            'card_number' => $cardData['number'],
            'cvv' => $cardData['cvv'],
        ];

        $gatewayResult = $this->paymentOrchestrator->charge($gatewayPayload);

        if (!$gatewayResult['success']) {
            return response()->json([
                'message' => 'Nenhum gateway conseguiu processar a transação.'
            ], 422);
        }

        $transaction = DB::transaction(function () use (
            $clientData,
            $gatewayResult,
            $amount,
            $cardData,
            $normalizedItems
        ) {
            $client = Client::query()->firstOrCreate(
                ['email' => $clientData['email']],
                ['name' => $clientData['name']]
            );

            $transaction = Transaction::query()->create([
                'client_id' => $client->id,
                'gateway_id' => $gatewayResult['gateway']->id,
                'external_id' => $gatewayResult['external_id'],
                'status' => 'paid',
                'refund_status' => 'not_requested',
                'amount' => $amount,
                'card_last_numbers' => substr($cardData['number'], -4),
                'gateway_response' => $gatewayResult['response'],
            ]);

            $transaction->products()->attach(
                collect($normalizedItems)->mapWithKeys(function ($item) {
                    return [
                        $item['product_id'] => [
                            'quantity' => $item['quantity'],
                            'unit_amount' => $item['unit_amount'],
                            'total_amount' => $item['total_amount'],
                        ]
                    ];
                })->toArray()
            );

            return $transaction->load(['client', 'gateway', 'products']);
        });

        return response()->json([
            'message' => 'Compra realizada com sucesso.',
            'data' => [
                'id' => $transaction->id,
                'external_id' => $transaction->external_id,
                'status' => $transaction->status,
                'refund_status' => $transaction->refund_status,
                'amount' => $transaction->amount,
                'card_last_numbers' => $transaction->card_last_numbers,
                'client' => [
                    'id' => $transaction->client->id,
                    'name' => $transaction->client->name,
                    'email' => $transaction->client->email,
                ],
                'gateway' => [
                    'id' => $transaction->gateway->id,
                    'name' => $transaction->gateway->name,
                    'slug' => $transaction->gateway->slug,
                ],
                'products' => $transaction->products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'amount' => $product->amount,
                        'quantity' => $product->pivot->quantity,
                        'unit_amount' => $product->pivot->unit_amount,
                        'total_amount' => $product->pivot->total_amount,
                    ];
                }),
            ]
        ], 201);
    }
}
