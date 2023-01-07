<?php declare(strict_types=1);

namespace Kadena\Pact;

use Carbon\Carbon;

final class Meta
{
    private const MIN_GAS_PRICE = 0.00000001;

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
            'creationTime' => $this->creationTime->getTimestamp(),
            'ttl' => $this->ttl,
            'gasLimit' => $this->gasLimit,
            'chainId' => $this->chainId,
            'gasPrice' => $this->gasPrice,
            'sender' => $this->sender,
        ];
    }

    public static function create(?array $options = []): self
    {
        return new self(
            creationTime: (isset($options['creationTime'])) ? Carbon::createFromTimestamp((int) $options['creationTime']) : Carbon::now(),
            ttl: (int) ($options['ttl'] ?? 7200),
            gasLimit: (int) ($options['gasLimit'] ?? 10000),
            chainId: (string) ($options['chainId'] ?? '0'),
            gasPrice: (float) ($options['gasPrice'] ?? self::MIN_GAS_PRICE),
            sender: (string) ($options['sender'] ?? '')
        );
    }
}
