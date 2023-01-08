<?php declare(strict_types=1);

namespace Kadena\ValueObjects\Command;

use Kadena\Contracts\Collection;
use Kadena\ValueObjects\HasCollectionMethods;

final class SignedCommandCollection implements Collection
{
    use HasCollectionMethods;

    public function __construct(SignedCommand ...$signedCommand)
    {
        $this->array = $signedCommand;
    }
}
