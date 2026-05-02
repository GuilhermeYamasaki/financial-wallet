<?php

namespace App\Domain\Money\Enums;

enum TransactionType: string
{
    case Deposit = 'deposit';
    case Transfer = 'transfer';
    case Reversal = 'reversal';
}
