<?php

namespace App\Http\Controllers\Api;

use App\Domain\Money\Actions\ReverseTransactionAction;
use App\Domain\Money\Exceptions\InvalidTransactionReversalException;
use App\Domain\Money\Exceptions\TransactionAlreadyReversedException;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

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

    public function show(Request $request, Transaction $transaction): JsonResponse
    {
        $wallet = $request->user()->wallet;

        abort_if(
            $transaction->wallet_from_id !== $wallet->id && $transaction->wallet_to_id !== $wallet->id,
            Response::HTTP_NOT_FOUND
        );

        return response()->json([
            'transaction' => $transaction->load(['walletFrom.user', 'walletTo.user', 'reversedTransaction']),
        ]);
    }

    public function reverse(Request $request, Transaction $transaction, ReverseTransactionAction $reverseTransaction): JsonResponse
    {
        $wallet = $request->user()->wallet;

        abort_if(
            $transaction->wallet_from_id !== $wallet->id && $transaction->wallet_to_id !== $wallet->id,
            Response::HTTP_NOT_FOUND
        );

        try {
            $reversalTransaction = $reverseTransaction->execute($transaction);
        } catch (TransactionAlreadyReversedException|InvalidTransactionReversalException $exception) {
            throw ValidationException::withMessages([
                'transaction' => [$exception->getMessage()],
            ]);
        }

        return response()->json([
            'message' => 'Transação revertida com sucesso.',
            'transaction' => $reversalTransaction->load(['walletFrom.user', 'walletTo.user', 'reversedTransaction']),
        ], Response::HTTP_CREATED);
    }
}
