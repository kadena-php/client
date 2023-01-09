<?php declare(strict_types=1);

namespace Kadena\DataMappers;

use JsonException;
use Kadena\Contracts\DataMappers\SignedCommandCollectionMapper as SignedCommandCollectionMapperContract;
use Kadena\ValueObjects\Command\SignedCommand;
use Kadena\ValueObjects\Command\SignedCommandCollection;

final class SignedCommandCollectionMapper implements SignedCommandCollectionMapperContract
{
    /**
     * @throws JsonException
     */
    public static function toArray(SignedCommandCollection $commands): array
    {
        return [
            'cmds' => array_map(static function (SignedCommand $command) {
                return SignedCommandMapper::toArray($command);
            }, $commands->toArray())
        ];
    }

    /**
     * @throws JsonException
     */
    public static function toString(SignedCommandCollection $commands): string
    {
        return json_encode(self::toArray($commands), JSON_THROW_ON_ERROR);
    }
}
