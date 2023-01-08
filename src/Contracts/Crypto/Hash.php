<?php declare(strict_types=1);

namespace Kadena\Contracts\Crypto;

interface Hash
{
    public static function generic(string $string): string;
}
