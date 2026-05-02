<?php

namespace App\Domain\Money\Exceptions;

use DomainException;

class InsufficientBalanceException extends DomainException
{
    protected $message = 'Saldo insuficiente para realizar a transferência.';
}
