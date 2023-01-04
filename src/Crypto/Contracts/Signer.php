<?php declare(strict_types=1);

namespace Kadena\Crypto\Contracts;

use Kadena\Crypto\KeyPair;
use Kadena\Crypto\Signature;
use Kadena\Pact\Command;
use ParagonIE\Halite\Asymmetric\SignaturePublicKey;

interface Signer
{
    public static function sign(string $message, KeyPair $keyPair): Signature;

    public static function signCommand(Command $command, KeyPair $keyPair): Signature;

    public static function signHash(string $hash, KeyPair $keyPair): Signature;

    public static function verifySignature(string $message, string $signature, SignaturePublicKey $publicKey): bool;
}
