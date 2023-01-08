<?php declare(strict_types=1);

namespace Kadena\Crypto;

use JsonException;
use Kadena\DataMappers\CommandMapper;
use Kadena\ValueObjects\Command\Command;
use Kadena\ValueObjects\Command\SignedCommand;
use Kadena\ValueObjects\Signer\KeyPair;
use Kadena\ValueObjects\Signer\KeyPairCollection;
use Kadena\ValueObjects\Signer\SignatureCollection;
use ParagonIE\ConstantTime\Base64UrlSafe;
use ParagonIE\Halite\Alerts\InvalidType;
use SodiumException;

final class CommandSigner
{
    /**
     * @throws InvalidType
     * @throws SodiumException
     * @throws JsonException
     */
    public static function sign(Command $command, KeyPairCollection $keyPairs): SignedCommand
    {
        $commandString = CommandMapper::toString($command);
        $commandHash = Base64UrlSafe::encodeUnpadded(Hash::generic($commandString));

        $signatures = [];

        /** @var KeyPair $keyPair */
        foreach ($keyPairs->toArray() as $keyPair) {
            $signatures[] = MessageSigner::sign($commandString, $keyPair);
        }

        return new SignedCommand(
            hash: $commandHash,
            signatures: new SignatureCollection(...$signatures),
            command: $command
        );
    }
}
