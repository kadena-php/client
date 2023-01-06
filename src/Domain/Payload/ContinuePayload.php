<?php declare(strict_types=1);

namespace Kadena\Domain\Payload;

final class ContinuePayload
{
    public function __construct(
        public readonly string $pactId,
        public readonly bool $rollback,
        public readonly int $step,
        public readonly ?string $proof = null,
        public readonly array $data = [],
    ) {
    }

    public function toArray(): array
    {
        return [
            'proof' => $this->proof,
            'pactId' => $this->pactId,
            'rollback' => $this->rollback,
            'step' => $this->step,
            'data' => $this->data,
        ];
    }
}
