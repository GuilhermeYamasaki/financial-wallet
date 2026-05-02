<?php

namespace App\Domain\Money\Exceptions;

use DomainException;

class TransactionAlreadyReversedException extends DomainException
{
    protected $message = 'Esta transação já foi revertida.';
}
