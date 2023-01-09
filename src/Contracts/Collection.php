<?php declare(strict_types=1);

namespace Kadena\Contracts;

interface Collection
{
    public function toArray(): array;
    public function first(): mixed;
    public function get(int $offset): mixed;
}
