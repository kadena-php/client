<?php declare(strict_types=1);

namespace Kadena\ValueObjects\Command;

use Kadena\ValueObjects\Command\Payload\Payload;
use Kadena\ValueObjects\Signer\SignerCollection;

final class Command
{
    public function __construct(
        public readonly Metadata $meta,
        public readonly Payload  $payload,
        public readonly string   $networkId,
        public readonly string   $nonce,
        public readonly SignerCollection $signers,
    ) {
    }
}
