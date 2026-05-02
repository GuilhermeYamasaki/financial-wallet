<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json([
            'wallet' => $request->user()->wallet,
        ]);
    }
}
