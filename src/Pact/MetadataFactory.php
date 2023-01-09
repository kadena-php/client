<?php declare(strict_types=1);

namespace Kadena\Pact;

use Carbon\Carbon;
use Kadena\Contracts\Pact\MetadataFactory as MetadataFactoryContract;
use Kadena\ValueObjects\Command\Metadata;

final class MetadataFactory implements MetadataFactoryContract
{
    private Carbon $creationTime;
    private int $ttl = 7200;
    private int $gasLimit = 10000;
    private string $chainId = '0';
    private float $gasPrice = 1e-8;
    private string $sender = '';

    public function withOptions(array $options): self
    {
        foreach ($options as $key => $option) {
            if (in_array($key, array_keys(get_class_vars(self::class)), true)) {
                if ($key === 'creationTime' && ! is_a($option, Carbon::class)) {
                    $this->$key = Carbon::createFromTimestamp((int) $option);
                } else {
                    $this->$key = $option;
                }
            }
        }

        return $this;
    }

    public function make(): Metadata
    {
        if (! isset($this->creationTime)) {
            $this->creationTime = Carbon::now();
        }

        return new Metadata(
            creationTime: $this->creationTime,
            ttl: $this->ttl,
            gasLimit: $this->gasLimit,
            chainId: $this->chainId,
            gasPrice: $this->gasPrice,
            sender: $this->sender
        );
    }
}
