<?php

namespace App\Domain\Money\Enums;

enum TransactionStatus: string
{
    case Completed = 'completed';
    case Reversed = 'reversed';
    case Failed = 'failed';
}
