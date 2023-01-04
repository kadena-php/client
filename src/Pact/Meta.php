<?php declare(strict_types=1);

namespace Kadena\Pact;

use Carbon\Carbon;

final class Meta
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

    public function toArray(): array
    {
        return [
            'creationTime' => (int) $this->creationTime->getTimestamp(),
            'ttl' => $this->ttl,
            'gasLimit' => $this->gasLimit,
            'chainId' => $this->chainId,
            'gasPrice' => $this->gasPrice,
            'sender' => $this->sender,
        ];
    }
}
