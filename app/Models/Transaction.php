<?php

namespace App\Models;

use App\Domain\Money\Enums\TransactionStatus;
use App\Domain\Money\Enums\TransactionType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'type',
    'status',
    'amount',
    'wallet_from_id',
    'wallet_to_id',
    'reversed_transaction_id',
    'description',
])]
class Transaction extends Model
{
    use HasFactory;

    public function walletFrom(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'wallet_from_id');
    }

    public function walletTo(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'wallet_to_id');
    }

    public function reversedTransaction(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reversed_transaction_id');
    }

    public function reversalTransaction(): HasOne
    {
        return $this->hasOne(self::class, 'reversed_transaction_id');
    }

    protected function casts(): array
    {
        return [
            'type' => TransactionType::class,
            'status' => TransactionStatus::class,
            'amount' => 'integer',
        ];
    }
}
