<?php declare(strict_types=1);

namespace Kadena\Crypto;

use Kadena\Contracts\Crypto\KeyFactory as KeyFactoryContract;
use Kadena\ValueObjects\Signer\KeyPair;
use Kadena\ValueObjects\Signer\PublicKey;
use Kadena\ValueObjects\Signer\SecretKey;
use ParagonIE\Halite\Alerts\CannotPerformOperation;
use ParagonIE\Halite\Alerts\InvalidKey;
use SodiumException;

final class KeyFactory implements KeyFactoryContract
{
    /**
     * @throws InvalidKey
     * @throws CannotPerformOperation
     * @throws SodiumException
     */
    public static function generate(): KeyPair
    {
        $keyPair = \ParagonIE\Halite\KeyFactory::generateSignatureKeyPair();

        return new KeyPair(
            publicKey: new PublicKey($keyPair->getPublicKey()),
            secretKey: new SecretKey($keyPair->getSecretKey())
        );
    }
}
