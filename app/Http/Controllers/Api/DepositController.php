<?php

namespace App\Http\Controllers\Api;

use App\Domain\Money\Actions\DepositMoneyAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\DepositRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DepositController extends Controller
{
    public function __construct(private readonly DepositMoneyAction $depositMoney) {}

    public function store(DepositRequest $request): JsonResponse
    {
        $transaction = $this->depositMoney->execute(
            $request->user(),
            $request->integer('amount'),
            $request->validated('description'),
        );

        return response()->json([
            'message' => 'Depósito realizado com sucesso.',
            'wallet' => $request->user()->wallet()->first(),
            'transaction' => $transaction,
        ], Response::HTTP_CREATED);
    }
}
