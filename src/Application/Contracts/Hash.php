<?php declare(strict_types=1);

namespace Kadena\Application\Contracts;

interface Hash
{
    public static function generic(string $string): string;
}
