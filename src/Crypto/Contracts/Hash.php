<?php declare(strict_types=1);

namespace Kadena\Crypto\Contracts;

interface Hash
{
    public static function generic(string $string): string;
}
