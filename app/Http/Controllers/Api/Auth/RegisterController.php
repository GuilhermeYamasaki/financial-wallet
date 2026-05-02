<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends Controller
{
    public function __invoke(RegisterRequest $request): JsonResponse
    {
        $user = DB::transaction(function () use ($request) {
            $user = User::query()->create($request->validated());

            $user->wallet()->create(['balance' => 0]);

            return $user;
        });

        return response()->json([
            'message' => 'Usuário cadastrado com sucesso.',
        ], Response::HTTP_CREATED);
    }
}
