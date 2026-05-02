<?php

namespace App\Domain\Money\Exceptions;

use DomainException;

class InvalidTransactionReversalException extends DomainException
{
    protected $message = 'Não foi possível reverter esta transação.';
}
