<?php

declare(strict_types=1);

namespace App\Logging;

use Illuminate\Http\Request;
use Monolog\LogRecord;

final class RequestContextProcessor
{
    public function __construct(
        private readonly Request $request,
    ) {
    }

    public function __invoke(LogRecord $record): LogRecord
    {
        $context = array_merge([
            'user_id' => $this->request->user()?->getAuthIdentifier(),
            'ip' => $this->request->ip(),
            'method' => $this->request->method(),
            'path' => $this->request->path(),
        ], $this->redact($record->context));

        return $record->with(context: $context);
    }

    /** @param array<string, mixed> $context
     *  @return array<string, mixed>
     */
    private function redact(array $context): array
    {
        $redacted = [];

        foreach ($context as $key => $value) {
            if ($this->isSensitiveKey((string) $key)) {
                $redacted[$key] = '[REDACTED]';
                continue;
            }

            $redacted[$key] = is_array($value)
                ? $this->redact($value)
                : $value;
        }

        return $redacted;
    }

    private function isSensitiveKey(string $key): bool
    {
        return preg_match('/password|token|cookie|csrf/i', $key) === 1;
    }
}
