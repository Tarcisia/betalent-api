<?php

namespace App\Http\Controllers;

use App\Models\Gateway;
use App\Http\Requests\Gateway\UpdateGatewayPriorityRequest;

class GatewayController extends Controller
{
    public function index()
    {
        $gateways = Gateway::query()
            ->orderBy('priority')
            ->get();

        return response()->json($gateways);
    }

    public function toggle(Gateway $gateway)
    {
        $gateway->update([
            'is_active' => !$gateway->is_active,
        ]);

        return response()->json([
            'message' => 'Status do gateway atualizado com sucesso.',
            'data' => $gateway->fresh(),
        ]);
    }

    public function updatePriority(UpdateGatewayPriorityRequest $request, Gateway $gateway)
    {
        $gateway->update([
            'priority' => $request->integer('priority'),
        ]);

        return response()->json([
            'message' => 'Prioridade do gateway atualizada com sucesso.',
            'data' => $gateway->fresh(),
        ]);
    }
}