<?php declare(strict_types=1);

namespace Kadena\Crypto;

use Kadena\Crypto\Contracts\Signer as SignerContract;
use Kadena\Pact\Command;
use ParagonIE\ConstantTime\Base64UrlSafe;
use ParagonIE\ConstantTime\Hex;
use ParagonIE\Halite\Alerts\InvalidSignature;
use ParagonIE\Halite\Alerts\InvalidType;
use ParagonIE\Halite\Asymmetric\Crypto;
use ParagonIE\Halite\Asymmetric\SignaturePublicKey;
use ParagonIE\Halite\Halite;
use SodiumException;

final class Signer implements SignerContract
{
    /**
     * Sign a message given a key pair and return a SignedMessage object
     *
     * @throws InvalidType
     * @throws SodiumException
     */
    public static function sign(string $message, KeyPair $keyPair): Signature
    {
        $hash = Hash::generic($message);

        return self::signHash($hash, $keyPair);
    }

    /**
     * Sign a hash given a key pair and return a SignedMessage object
     *
     * @throws InvalidType
     * @throws SodiumException
     */
    public static function signHash(string $hash, KeyPair $keyPair): Signature
    {
        $signature = Crypto::sign($hash, $keyPair->secretKey, Halite::ENCODE_HEX);

        return new Signature(
            hash: Base64UrlSafe::encodeUnpadded($hash),
            signature: $signature,
            publicKey: Hex::encode($keyPair->publicKey->getRawKeyMaterial())
        );
    }

    /**
     * Verify a signature given a public key and message
     *
     * @throws InvalidType
     * @throws InvalidSignature
     * @throws SodiumException
     */
    public static function verifySignature(string $message, string $signature, SignaturePublicKey $publicKey): bool
    {
        return Crypto::verify($message, $publicKey, $signature, Halite::ENCODE_HEX);
    }

    /**
     * @throws InvalidType
     * @throws SodiumException
     */
    public static function signCommand(Command $command, KeyPair $keyPair): Signature
    {
        $message = $command->toString();

        return self::sign($message, $keyPair);
    }
}
