<?php

namespace App\Domain\Money\Actions;

use App\Domain\Money\Enums\TransactionStatus;
use App\Domain\Money\Enums\TransactionType;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class DepositMoneyAction
{
    public function execute(User $user, int $amount, ?string $description = null): Transaction
    {
        return DB::transaction(function () use ($user, $amount, $description) {
            $wallet = Wallet::query()
                ->whereBelongsTo($user)
                ->lockForUpdate()
                ->firstOrFail();

            $wallet->increment('balance', $amount);

            return Transaction::query()->create([
                'type' => TransactionType::Deposit,
                'status' => TransactionStatus::Completed,
                'amount' => $amount,
                'wallet_to_id' => $wallet->id,
                'description' => $description,
            ]);
        });
    }
}
