<?php

declare(strict_types=1);

namespace App\Exceptions;

use Throwable;

class CustomerNotFoundException extends ApiBaseException implements ExceptionWithStatusCodeInterface
{
    public function __construct($message = "Customer not found", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode(): int
    {
        return 404;
    }
}
