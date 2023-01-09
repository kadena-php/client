<?php declare(strict_types=1);

namespace Kadena\ValueObjects\Command\Payload;

final class ExecutePayload
{
    public function __construct(
        public readonly string $code,
        public readonly array $data = [],
    ) {
    }
}
