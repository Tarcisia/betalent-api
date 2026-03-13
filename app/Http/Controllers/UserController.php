<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;

class UserController extends Controller
{
    public function index()
    {
        $users = User::query()->latest()->paginate(10);

        return response()->json($users);
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);

        return response()->json([
            'message' => 'Usuário criado com sucesso.',
            'data' => $user,
        ], 201);
    }

    public function show(User $user)
    {
        return response()->json($user);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
        ];

        if (!empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        $user->update($payload);

        return response()->json([
            'message' => 'Usuário atualizado com sucesso.',
            'data' => $user->fresh(),
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'message' => 'Usuário excluído com sucesso.'
        ]);
    }
}