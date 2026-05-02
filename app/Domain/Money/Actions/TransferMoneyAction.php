<?php

namespace App\Domain\Money\Actions;

use App\Domain\Money\Enums\TransactionStatus;
use App\Domain\Money\Enums\TransactionType;
use App\Domain\Money\Exceptions\InsufficientBalanceException;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class TransferMoneyAction
{
    public function execute(User $sender, User $recipient, int $amount, ?string $description = null): Transaction
    {
        return DB::transaction(function () use ($sender, $recipient, $amount, $description) {
            $wallets = Wallet::query()
                ->whereIn('user_id', [$sender->id, $recipient->id])
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('user_id');

            $senderWallet = $wallets->get($sender->id);
            $recipientWallet = $wallets->get($recipient->id);

            if ($senderWallet->balance < $amount) {
                throw new InsufficientBalanceException;
            }

            $senderWallet->decrement('balance', $amount);
            $recipientWallet->increment('balance', $amount);

            return Transaction::query()->create([
                'type' => TransactionType::Transfer,
                'status' => TransactionStatus::Completed,
                'amount' => $amount,
                'wallet_from_id' => $senderWallet->id,
                'wallet_to_id' => $recipientWallet->id,
                'description' => $description,
            ]);
        });
    }
}
