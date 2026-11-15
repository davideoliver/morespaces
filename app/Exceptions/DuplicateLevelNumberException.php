<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class DuplicateLevelNumberException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Já existe um nível com este número.');
    }
}
