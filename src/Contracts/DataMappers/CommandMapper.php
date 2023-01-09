<?php declare(strict_types=1);

namespace Kadena\Contracts\DataMappers;

use Kadena\ValueObjects\Command\Command;

interface CommandMapper
{
    public static function toArray(Command $command): array;
    public static function toString(Command $command): string;
    public static function fromString(string $commandJson): Command;
}