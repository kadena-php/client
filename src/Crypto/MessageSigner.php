<?php declare(strict_types=1);

namespace Kadena\Crypto;

use Kadena\Contracts\Crypto\MessageSigner as MessageSignerContract;
use Kadena\ValueObjects\Signer\KeyPair;
use Kadena\ValueObjects\Signer\Signature;
use ParagonIE\ConstantTime\Base64UrlSafe;
use ParagonIE\Halite\Alerts\InvalidSignature;
use ParagonIE\Halite\Alerts\InvalidType;
use ParagonIE\Halite\Asymmetric\Crypto;
use ParagonIE\Halite\Asymmetric\SignaturePublicKey;
use ParagonIE\Halite\Halite;
use SodiumException;

final class MessageSigner implements MessageSignerContract
{
    /**
     * @throws InvalidType
     * @throws SodiumException
     */
    public static function sign(string $message, KeyPair $keyPair): Signature
    {
        $hash = Hash::generic($message);

        return self::signHash($hash, $keyPair);
    }

    /**
     * @throws InvalidType
     * @throws SodiumException
     */
    public static function signHash(string $hash, KeyPair $keyPair): Signature
    {
        $signature = Crypto::sign($hash, $keyPair->secretKey->key, Halite::ENCODE_HEX);

        return new Signature(
            hash: Base64UrlSafe::encodeUnpadded($hash),
            signature: $signature,
            publicKey: $keyPair->publicKey->toString()
        );
    }

    /**
     * @throws InvalidType
     * @throws InvalidSignature
     * @throws SodiumException
     */
    public static function verifySignature(string $message, string $signature, SignaturePublicKey $publicKey): bool
    {
        return Crypto::verify($message, $publicKey, $signature, Halite::ENCODE_HEX);
    }
}
