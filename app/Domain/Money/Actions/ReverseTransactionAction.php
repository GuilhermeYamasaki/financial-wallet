<?php

namespace App\Domain\Money\Actions;

use App\Domain\Money\Enums\TransactionStatus;
use App\Domain\Money\Enums\TransactionType;
use App\Domain\Money\Exceptions\InvalidTransactionReversalException;
use App\Domain\Money\Exceptions\TransactionAlreadyReversedException;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class ReverseTransactionAction
{
    public function execute(Transaction $transaction): Transaction
    {
        return DB::transaction(function () use ($transaction) {
            $originalTransaction = Transaction::query()
                ->whereKey($transaction->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($originalTransaction->status === TransactionStatus::Reversed) {
                throw new TransactionAlreadyReversedException;
            }

            if ($originalTransaction->type === TransactionType::Reversal) {
                throw new InvalidTransactionReversalException;
            }

            $walletIds = collect([
                $originalTransaction->wallet_from_id,
                $originalTransaction->wallet_to_id,
            ])->filter()->sort()->values();

            if ($walletIds->isEmpty()) {
                throw new InvalidTransactionReversalException;
            }

            $wallets = Wallet::query()
                ->whereIn('id', $walletIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $walletFromId = $originalTransaction->wallet_to_id;
            $walletToId = $originalTransaction->wallet_from_id;

            if ($walletFromId) {
                $wallets->get($walletFromId)->decrement('balance', $originalTransaction->amount);
            }

            if ($walletToId) {
                $wallets->get($walletToId)->increment('balance', $originalTransaction->amount);
            }

            $reversalTransaction = Transaction::query()->create([
                'type' => TransactionType::Reversal,
                'status' => TransactionStatus::Completed,
                'amount' => $originalTransaction->amount,
                'wallet_from_id' => $walletFromId,
                'wallet_to_id' => $walletToId,
                'reversed_transaction_id' => $originalTransaction->id,
                'description' => 'Reversão da transação '.$originalTransaction->id,
            ]);

            $originalTransaction->update([
                'status' => TransactionStatus::Reversed,
            ]);

            return $reversalTransaction;
        });
    }
}
