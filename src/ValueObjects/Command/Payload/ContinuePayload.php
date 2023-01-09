<?php declare(strict_types=1);

namespace Kadena\ValueObjects\Command\Payload;

final class ContinuePayload
{
    public function __construct(
        public readonly string $pactId,
        public readonly bool $rollback,
        public readonly int $step,
        public readonly ?string $proof = null,
        public readonly array $data = [],
    ) {
    }
}
