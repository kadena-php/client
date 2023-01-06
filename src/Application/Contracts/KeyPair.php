<?php declare(strict_types=1);

namespace Kadena\Application\Contracts;

interface KeyPair
{
    public static function generate(): self;
}
