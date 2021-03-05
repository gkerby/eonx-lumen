<?php

declare(strict_types=1);

namespace App\Exceptions;

interface ExceptionWithStatusCodeInterface
{
    public function getStatusCode(): int;
}
