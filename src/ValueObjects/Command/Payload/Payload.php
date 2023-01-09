<?php declare(strict_types=1);

namespace Kadena\ValueObjects\Command\Payload;

use InvalidArgumentException;

final class Payload
{
    public function __construct(
        public readonly PayloadType $payloadType,
        public readonly ?ContinuePayload $continuePayload = null,
        public readonly ?ExecutePayload $executePayload = null,
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        if (
            $this->payloadType === PayloadType::EXECUTE
            && (! $this->executePayload || $this->continuePayload)
        ) {
            throw new InvalidArgumentException('Only execute payload should be provided when type is \'execute\'');
        }

        if (
            $this->payloadType === PayloadType::CONTINUE
            && (! $this->continuePayload || $this->executePayload)
        ) {
            throw new InvalidArgumentException('Only continue payload should be provided when type is \'continue\'');
        }
    }
}
