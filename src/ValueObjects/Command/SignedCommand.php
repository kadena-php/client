<?php declare(strict_types=1);

namespace Kadena\ValueObjects\Command;

use InvalidArgumentException;
use Kadena\ValueObjects\Signer\SignatureCollection;

final class SignedCommand
{
    public function __construct(
        public readonly string $hash,
        public readonly SignatureCollection $signatures,
        public readonly Command $command,
    ) {
        $this->validateSignatures();
    }

    private function validateSignatures(): void
    {
        foreach ($this->signatures->toArray() as $signature) {
            if ($signature->hash !== $this->hash) {
                throw new InvalidArgumentException("Signatures for different hashes found: {$signature->hash}, expected: {$this->hash}");
            }
        }
    }
}
