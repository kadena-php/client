<?php declare(strict_types=1);

namespace Kadena\ValueObjects\Signer;

use Kadena\Contracts\Collection;
use Kadena\Traits\HasCollectionMethods;

final class KeyPairCollection implements Collection
{
    use HasCollectionMethods;

    public function __construct(KeyPair ...$keyPair)
    {
        $this->array = $keyPair;
    }
}
