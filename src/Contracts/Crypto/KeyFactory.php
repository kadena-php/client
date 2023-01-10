<?php declare(strict_types=1);

namespace Kadena\Contracts\Crypto;

use Kadena\ValueObjects\Signer\KeyPair;
use Kadena\ValueObjects\Signer\PublicKey;
use Kadena\ValueObjects\Signer\SecretKey;

interface KeyFactory
{
    public static function generate(): KeyPair;

    public static function publicKeyFromBytes(string $publicKey): PublicKey;
    public static function publicKeyFromHex(string $publicKey): PublicKey;
    public static function secretKeyFromBytes(string $secretKey): SecretKey;
    public static function secretKeyFromHex(string $secretKey): SecretKey;
}
