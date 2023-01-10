<?php declare(strict_types=1);

namespace Kadena\Crypto;

use Kadena\Contracts\Crypto\KeyFactory as KeyFactoryContract;
use Kadena\ValueObjects\Signer\KeyPair;
use Kadena\ValueObjects\Signer\PublicKey;
use Kadena\ValueObjects\Signer\SecretKey;
use ParagonIE\ConstantTime\Hex;
use ParagonIE\Halite\Alerts\CannotPerformOperation;
use ParagonIE\Halite\Alerts\InvalidKey;
use ParagonIE\Halite\Asymmetric\SignaturePublicKey;
use ParagonIE\Halite\Asymmetric\SignatureSecretKey;
use ParagonIE\HiddenString\HiddenString;
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

    /**
     * @throws InvalidKey
     */
    public static function publicKeyFromBytes(string $publicKey): PublicKey
    {
        return new PublicKey(new SignaturePublicKey(new HiddenString($publicKey)));
    }

    /**
     * @throws InvalidKey
     */
    public static function publicKeyFromHex(string $publicKey): PublicKey
    {
        return self::publicKeyFromBytes(Hex::decode($publicKey));
    }

    /**
     * @throws InvalidKey
     */
    public static function secretKeyFromBytes(string $secretKey): SecretKey
    {
        return new SecretKey(new SignatureSecretKey(new HiddenString($secretKey)));
    }

    /**
     * @throws InvalidKey
     */
    public static function secretKeyFromHex(string $secretKey): SecretKey
    {
        return self::secretKeyFromBytes(Hex::decode($secretKey));
    }
}
