<?php

namespace App\Http\Controllers\Api;

use App\Domain\Money\Actions\TransferMoneyAction;
use App\Domain\Money\Exceptions\InsufficientBalanceException;
use App\Http\Controllers\Controller;
use App\Http\Requests\TransferRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class TransferController extends Controller
{
    public function store(TransferRequest $request, TransferMoneyAction $transferMoney): JsonResponse
    {
        $recipient = User::query()->findOrFail($request->integer('recipient_id'));

        try {
            $transaction = $transferMoney->execute(
                $request->user(),
                $recipient,
                $request->integer('amount'),
                $request->validated('description'),
            );
        } catch (InsufficientBalanceException $exception) {
            throw ValidationException::withMessages([
                'amount' => [$exception->getMessage()],
            ]);
        }

        return response()->json([
            'message' => 'Transferência realizada com sucesso.',
            'wallet' => $request->user()->wallet()->first(),
            'transaction' => $transaction,
        ], Response::HTTP_CREATED);
    }
}
