<?php declare(strict_types=1);

namespace Kadena\ValueObjects\Signer;

final class Signer
{
    public function __construct(
        public readonly PublicKey $publicKey,
        public readonly CapabilityCollection $capabilities,
    ) {
    }
}
