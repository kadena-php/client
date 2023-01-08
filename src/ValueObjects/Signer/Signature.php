<?php declare(strict_types=1);

namespace Kadena\ValueObjects\Signer;

final class Signature
{
    public function __construct(
        readonly public string $hash,
        readonly public string $signature,
        readonly public ?string $publicKey = null,
    ) {
    }
}
