<?php

namespace App\Http\Controllers\Api;

use App\Domain\Money\Actions\TransferMoneyAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\TransferRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TransferController extends Controller
{
    public function __construct(private readonly TransferMoneyAction $transferMoney) {}

    public function store(TransferRequest $request): JsonResponse
    {
        $recipient = User::query()->findOrFail($request->integer('recipient_id'));

        $transaction = $this->transferMoney->execute(
            $request->user(),
            $recipient,
            $request->integer('amount'),
            $request->validated('description'),
        );

        return response()->json([
            'message' => 'Transferência realizada com sucesso.',
            'wallet' => $request->user()->wallet()->first(),
            'transaction' => $transaction,
        ], Response::HTTP_CREATED);
    }
}
