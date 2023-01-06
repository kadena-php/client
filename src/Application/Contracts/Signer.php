<?php declare(strict_types=1);

namespace Kadena\Application\Contracts;

use Kadena\Application\Command;
use Kadena\Domain\Keypair\KeyPair;
use Kadena\Domain\Signature\Signature;
use ParagonIE\Halite\Asymmetric\SignaturePublicKey;

interface Signer
{
    public static function sign(string $message, KeyPair $keyPair): Signature;

    public static function signCommand(Command $command, KeyPair $keyPair): Signature;

    public static function signHash(string $hash, KeyPair $keyPair): Signature;

    public static function verifySignature(string $message, string $signature, SignaturePublicKey $publicKey): bool;
}
