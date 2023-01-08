<?php declare(strict_types=1);

namespace Kadena\Traits;

trait HasCollectionMethods
{
    private array $array = [];

    public function toArray(): array
    {
        return $this->array;
    }

    public function first(): mixed
    {
        return $this->array[0];
    }

    public function get(int $offset): mixed
    {
        return $this->array[$offset];
    }

    public function count(): int
    {
        return sizeof($this->array);
    }
}
