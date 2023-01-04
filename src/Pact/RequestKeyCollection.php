<?php declare(strict_types=1);

namespace Kadena\Pact;

final class RequestKeyCollection
{
    /**
     * @var RequestKey[]
     */
    private array $array;

    public function __construct(RequestKey ...$requestKey)
    {
        $this->array = $requestKey;
    }

    /**
     * @return RequestKey[]
     */
    public function toArray(): array
    {
        return $this->array;
    }

    public function toPlainArray(): array
    {
        return array_map(static function (RequestKey $requestKey) {
            return $requestKey->key;
        }, $this->array);
    }

    public function first(): RequestKey
    {
        return $this->array[0];
    }
}
