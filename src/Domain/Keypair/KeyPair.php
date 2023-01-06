<?php declare(strict_types=1);

namespace Kadena\Domain\Keypair;

use Kadena\Application\Contracts\KeyPair as KeyPairContract;
use ParagonIE\Halite\Alerts\CannotPerformOperation;
use ParagonIE\Halite\Alerts\InvalidKey;
use ParagonIE\Halite\Asymmetric\SignaturePublicKey;
use ParagonIE\Halite\Asymmetric\SignatureSecretKey;
use ParagonIE\Halite\KeyFactory;
use SodiumException;

final class KeyPair implements KeyPairContract
{
    public function __construct(
        readonly public SignaturePublicKey $publicKey,
        readonly public SignatureSecretKey $secretKey,
    ) {
    }

    /**
     * @throws InvalidKey
     * @throws CannotPerformOperation
     * @throws SodiumException
     */
    public static function generate(): self
    {
        $keyPair = KeyFactory::generateSignatureKeyPair();

        return new self(
            publicKey: $keyPair->getPublicKey(),
            secretKey: $keyPair->getSecretKey()
        );
    }
}
