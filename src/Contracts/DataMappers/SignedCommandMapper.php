<?php declare(strict_types=1);

namespace Kadena\Contracts\DataMappers;

use Kadena\ValueObjects\Command\SignedCommand;

interface SignedCommandMapper
{
    public static function toArray(SignedCommand $command): array;
    public static function toString(SignedCommand $command): string;
    public static function fromString(string $commandJson): SignedCommand;
}
