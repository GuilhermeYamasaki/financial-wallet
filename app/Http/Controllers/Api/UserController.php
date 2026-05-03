<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $users = User::query()
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'email']);

        return response()->json([
            'users' => $users,
        ]);
    }
}
