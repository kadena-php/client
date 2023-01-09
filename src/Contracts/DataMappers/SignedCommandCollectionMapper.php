<?php declare(strict_types=1);

namespace Kadena\Contracts\DataMappers;

use Kadena\ValueObjects\Command\SignedCommandCollection;

interface SignedCommandCollectionMapper
{
    public static function toArray(SignedCommandCollection $commands): array;
    public static function toString(SignedCommandCollection $commands): string;
}
