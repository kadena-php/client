<?php declare(strict_types=1);

namespace Kadena\ValueObjects\RequestKey;

use Kadena\Contracts\Collection;
use Kadena\ValueObjects\HasCollectionMethods;

final class RequestKeyCollection implements Collection
{
    use HasCollectionMethods;

    public function __construct(RequestKey ...$requestKey)
    {
        $this->array = $requestKey;
    }

    public function toPlainArray(): array
    {
        return array_map(static function (RequestKey $requestKey) {
            return $requestKey->key;
        }, $this->toArray());
    }
}
