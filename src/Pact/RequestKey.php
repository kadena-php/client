<?php declare(strict_types=1);

namespace Kadena\Pact;

final class RequestKey
{
    public function __construct(
        public readonly string $key,
    ) {
    }
}
