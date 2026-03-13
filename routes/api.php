<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\GatewayController;
use App\Http\Controllers\UserController;

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/purchases', [PurchaseController::class, 'store']);
Route::prefix('mock')->group(function () {
    Route::post('/gateway1/login', function () {
        return response()->json([
            'token' => 'mock-gateway-1-token'
        ]);
    });

    Route::post('/gateway1/transactions', function (Request $request) {
        $cvv = $request->input('cvv');

        if (in_array($cvv, ['100', '200'], true)) {
            return response()->json([
                'message' => 'Cartão inválido no gateway 1.'
            ], 422);
        }

        return response()->json([
            'id' => (string) Str::uuid(),
            'status' => 'paid',
            'amount' => $request->input('amount'),
            'name' => $request->input('name'),
            'email' => $request->input('email'),
        ]);
    });

    Route::post('/gateway1/transactions/{id}/charge_back', function (string $id) {
        return response()->json([
            'id' => $id,
            'status' => 'refunded'
        ]);
    });

    Route::post('/gateway2/transacoes', function (Request $request) {
        $cvv = $request->input('cvv');

        if (in_array($cvv, ['200', '300'], true)) {
            return response()->json([
                'message' => 'Cartão inválido no gateway 2.'
            ], 422);
        }

        return response()->json([
            'id' => (string) Str::uuid(),
            'status' => 'paid',
            'valor' => $request->input('valor'),
            'nome' => $request->input('nome'),
            'email' => $request->input('email'),
        ]);
    });

    Route::post('/gateway2/transacoes/reembolso', function (Request $request) {
        return response()->json([
            'id' => $request->input('id'),
            'status' => 'refunded'
        ]);
    });
   
});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', function (Request $request) {
        return response()->json($request->user());
    });
    Route::get('/admin-only', function () {
        return response()->json([
            'message' => 'Você é ADMIN.'
        ]);
    })->middleware('role:ADMIN');

    Route::apiResource('users', UserController::class)
       ->middleware('role:ADMIN,MANAGER');
       
    Route::apiResource('products', ProductController::class)
        ->middleware('role:ADMIN,MANAGER,FINANCE');

    Route::get('/clients', [ClientController::class, 'index'])
        ->middleware('role:ADMIN,MANAGER,FINANCE');

    Route::get('/clients/{client}', [ClientController::class, 'show'])
        ->middleware('role:ADMIN,MANAGER,FINANCE');

    Route::get('/transactions', [TransactionController::class, 'index'])
        ->middleware('role:ADMIN,MANAGER,FINANCE');

    Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])
        ->middleware('role:ADMIN,MANAGER,FINANCE');

    Route::post('/transactions/{transaction}/refund', [TransactionController::class, 'refund'])
        ->middleware('role:ADMIN,FINANCE');

    Route::get('/gateways', [GatewayController::class, 'index'])
        ->middleware('role:ADMIN');

    Route::patch('/gateways/{gateway}/toggle', [GatewayController::class, 'toggle'])
        ->middleware('role:ADMIN');

    Route::patch('/gateways/{gateway}/priority', [GatewayController::class, 'updatePriority'])
        ->middleware('role:ADMIN');

  
});