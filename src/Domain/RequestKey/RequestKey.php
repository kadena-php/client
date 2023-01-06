<?php declare(strict_types=1);

namespace Kadena\Domain\RequestKey;

final class RequestKey
{
    public function __construct(
        public readonly string $key,
    ) {
    }
}
