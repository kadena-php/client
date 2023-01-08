<?php declare(strict_types=1);

namespace Kadena\ValueObjects\Signer;

use Kadena\Contracts\Collection;
use Kadena\Traits\HasCollectionMethods;

final class SignatureCollection implements Collection
{
    use HasCollectionMethods;

    public function __construct(Signature ...$signature)
    {
        $this->array = $signature;
    }
}
