<?php declare(strict_types=1);

namespace Kadena\Contracts\Crypto;

use Kadena\ValueObjects\Signer\KeyPair;
use Kadena\ValueObjects\Signer\Signature;
use ParagonIE\Halite\Asymmetric\SignaturePublicKey;

interface MessageSigner
{
    public static function sign(string $message, KeyPair $keyPair): Signature;

    public static function signHash(string $hash, KeyPair $keyPair): Signature;

    public static function verifySignature(string $message, string $signature, SignaturePublicKey $publicKey): bool;
}
