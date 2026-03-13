<?php

namespace App\Http\Controllers;

use App\Models\Client;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::query()->latest()->paginate(10);

        return response()->json($clients);
    }

    public function show(Client $client)
    {
        $client->load([
            'transactions.gateway',
            'transactions.products',
        ]);

        return response()->json($client);
    }
}
