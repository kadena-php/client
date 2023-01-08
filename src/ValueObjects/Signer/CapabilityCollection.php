<?php declare(strict_types=1);

namespace Kadena\ValueObjects\Signer;

use Kadena\Contracts\Collection;
use Kadena\Traits\HasCollectionMethods;

final class CapabilityCollection implements Collection
{
    use HasCollectionMethods;

    public function __construct(Capability ...$capabilityDescription)
    {
        $this->array = $capabilityDescription;
    }
}
