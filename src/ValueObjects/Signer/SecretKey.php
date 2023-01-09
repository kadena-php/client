<?php declare(strict_types=1);

namespace Kadena\ValueObjects\Signer;

use ParagonIE\Halite\Asymmetric\SignatureSecretKey;

final class SecretKey
{
    public function __construct(
        readonly public SignatureSecretKey $key
    ) {
    }
}
