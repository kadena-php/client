<?php declare(strict_types=1);

namespace Kadena\ValueObjects\Command;

use Carbon\Carbon;

final class Metadata
{
    public function __construct(
        public readonly Carbon $creationTime,
        public readonly int $ttl,
        public readonly int $gasLimit,
        public readonly string $chainId,
        public readonly float $gasPrice,
        public readonly string $sender,
    ) {
    }
}
