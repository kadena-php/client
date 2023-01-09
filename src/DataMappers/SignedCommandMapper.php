<?php declare(strict_types=1);

namespace Kadena\DataMappers;

use InvalidArgumentException;
use JsonException;
use Kadena\Contracts\DataMappers\SignedCommandMapper as SignedCommandMapperContract;
use Kadena\ValueObjects\Command\SignedCommand;
use Kadena\ValueObjects\Signer\Signature;
use Kadena\ValueObjects\Signer\SignatureCollection;

final class SignedCommandMapper implements SignedCommandMapperContract
{
    /**
     * @throws JsonException
     */
    public static function toArray(SignedCommand $command): array
    {
        return [
            'cmd' => CommandMapper::toString($command->command),
            'hash' => $command->hash,
            'sigs' => array_map(static function (Signature $signature) {
                return ['sig' => $signature->signature];
            }, $command->signatures->toArray())
        ];
    }

    /**
     * @throws JsonException
     */
    public static function toString(SignedCommand $command): string
    {
        return json_encode(self::toArray($command), JSON_THROW_ON_ERROR);
    }

    /**
     * @throws JsonException
     */
    public static function fromString(string $commandJson): SignedCommand
    {
        $command = json_decode($commandJson, false, 512, JSON_THROW_ON_ERROR);

        if (! isset($command->hash, $command->sigs, $command->cmd)) {
            throw new InvalidArgumentException('Invalid Signed Command JSON string given');
        }

        return new SignedCommand(
            hash: $command->hash,
            signatures: self::getSignatures((array) $command->sigs, $command->hash),
            command: CommandMapper::fromString($command->cmd),
        );
    }

    private static function getSignatures(array $signatures, string $hash): SignatureCollection
    {
        return new SignatureCollection(...array_map(static function (array|object $signature) use ($hash) {
            $signature = (array) $signature;

            return new Signature(
                hash: $hash,
                signature: $signature['sig']
            );
        }, $signatures));
    }
}
