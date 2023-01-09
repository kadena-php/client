<?php declare(strict_types=1);

namespace Kadena\ValueObjects\Signer;

use ParagonIE\ConstantTime\Hex;
use ParagonIE\Halite\Asymmetric\SignaturePublicKey;

final class PublicKey
{
    public function __construct(
        readonly public SignaturePublicKey $key
    ) {
    }

    public function toString(): string
    {
        return Hex::encode($this->key->getRawKeyMaterial());
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
