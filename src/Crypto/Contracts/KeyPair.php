<?php declare(strict_types=1);

namespace Kadena\Crypto\Contracts;

interface KeyPair
{
    public static function generate(): self;
}
