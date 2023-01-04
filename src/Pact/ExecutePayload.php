<?php declare(strict_types=1);

namespace Kadena\Pact;

final class ExecutePayload
{
    public function __construct(
        public readonly string $code,
        public readonly array $data = [],
    ) {
    }

    public function toArray(): array
    {
        return [
            'data' => $this->data,
            'code' => $this->code,
        ];
    }
}
