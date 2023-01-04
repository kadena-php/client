<?php declare(strict_types=1);

namespace Kadena\Pact;

use InvalidArgumentException;
use JsonException;
use Kadena\Crypto\SignatureCollection;

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

    /**
     * @throws JsonException
     */
    public function toArray(): array
    {
        $signatures = [];

        foreach ($this->signatures->toArray() as $signature) {
            $signatures[] = ['sig' => $signature->signature];
        }
        return [
            'hash' => $this->hash,
            'sigs' => $signatures,
            'cmd' => $this->command->toString(),
        ];
    }

    /**
     * @throws JsonException
     */
    public function toString(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
    }

    /**
     * @throws JsonException
     */
    public static function fromString(string $commandJson): self
    {
        $command = json_decode($commandJson, false, 512, JSON_THROW_ON_ERROR);

        if (! isset($command->hash, $command->sigs, $command->cmd)) {
            throw new InvalidArgumentException('Invalid Signed Command JSON string given');
        }

        return new self(
            hash: $command->hash,
            signatures: SignatureCollection::fromArray((array) $command->sigs, $command->hash),
            command: Command::fromString($command->cmd),
        );
    }
}
