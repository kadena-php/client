<?php declare(strict_types=1);

namespace Kadena\ValueObjects\Signer;

final class Capability
{
    public function __construct(
        public readonly string  $name,
        public readonly array   $arguments = [],
    ) {
    }
}
