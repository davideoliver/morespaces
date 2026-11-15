<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        if (! str_starts_with(static::class, 'Tests\\Feature\\Security\\')) {
            $this->withoutMiddleware(ValidateCsrfToken::class);
        }
    }
}
