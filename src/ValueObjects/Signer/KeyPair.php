<?php declare(strict_types=1);

namespace Kadena\ValueObjects\Signer;

final class KeyPair
{
    public function __construct(
        readonly public PublicKey $publicKey,
        readonly public SecretKey $secretKey,
    ) {
    }
}
