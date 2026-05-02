<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $wallet = $request->user()->wallet;

        $transactions = Transaction::query()
            ->with(['walletFrom.user', 'walletTo.user'])
            ->where('wallet_from_id', $wallet->id)
            ->orWhere('wallet_to_id', $wallet->id)
            ->latest()
            ->get();

        return response()->json([
            'transactions' => $transactions,
        ]);
    }
}
