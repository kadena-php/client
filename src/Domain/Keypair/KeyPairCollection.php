<?php declare(strict_types=1);

namespace Kadena\Domain\Keypair;

final class KeyPairCollection
{
    /**
     * @var KeyPair[]
     */
    private array $array;

    public function __construct(KeyPair ...$keyPair)
    {
        $this->array = $keyPair;
    }

    /**
     * @return KeyPair[]
     */
    public function toArray(): array
    {
        return $this->array;
    }
}
