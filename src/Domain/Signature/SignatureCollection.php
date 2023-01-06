<?php declare(strict_types=1);

namespace Kadena\Domain\Signature;

final class SignatureCollection
{
    /**
     * @var Signature[]
     */
    private array $array;

    public function __construct(Signature ...$signature)
    {
        $this->array = $signature;
    }

    public function first(): Signature
    {
        return $this->array[0];
    }

    /**
     * @return Signature[]
     */
    public function toArray(): array
    {
        return $this->array;
    }

    public static function fromArray(array $signatures, string $hash): self
    {
        return new self(...array_map(static function (array|object $signature) use ($hash) {
            $signature = (array) $signature;

            return new Signature(
                hash: $hash,
                signature: $signature['sig']
            );
        }, $signatures));
    }
}
