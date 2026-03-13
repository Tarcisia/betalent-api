<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\RefundService;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\TransactionDetailResource;

class TransactionController extends Controller
{
    public function __construct(private RefundService $refundService) 
    {
    }

    public function index()
    {
        $transactions = Transaction::query()
            ->with(['client', 'gateway'])
            ->latest()
            ->paginate(10);

        return TransactionResource::collection($transactions);
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['client', 'gateway', 'products']);

        return new TransactionDetailResource($transaction);
    }

    public function refund(Transaction $transaction)
    {
        $updatedTransaction = $this->refundService->execute($transaction);

        return response()->json([
            'message' => 'Reembolso processado com sucesso.',
            'data' => new TransactionDetailResource($updatedTransaction),
        ]);
    }
}