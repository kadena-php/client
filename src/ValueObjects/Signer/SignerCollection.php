<?php declare(strict_types=1);

namespace Kadena\ValueObjects\Signer;

use Kadena\Contracts\Collection;
use Kadena\ValueObjects\HasCollectionMethods;

final class SignerCollection implements Collection
{
    use HasCollectionMethods;

    public function __construct(Signer ...$signer)
    {
        $this->array = $signer;
    }
}
