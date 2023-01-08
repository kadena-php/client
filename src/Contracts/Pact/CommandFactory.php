<?php declare(strict_types=1);

namespace Kadena\Contracts\Pact;

use Kadena\ValueObjects\Command\Command;
use Kadena\ValueObjects\Command\Metadata;
use Kadena\ValueObjects\Command\Payload\ContinuePayload;
use Kadena\ValueObjects\Command\Payload\ExecutePayload;
use Kadena\ValueObjects\Signer\Signer;
use Kadena\ValueObjects\Signer\SignerCollection;

interface CommandFactory
{
    public static function load(Command $command): self;
    public function addSigner(Signer $signer): self;
    public function withMetadata(Metadata $metadata): self;
    public function withExecutePayload(ExecutePayload $payload): self;
    public function withContinuePayload(ContinuePayload $payload): self;
    public function withNonce(string $nonce): self;
    public function withNetworkId(string $networkId): self;
    public function withSigners(SignerCollection $signers): self;
    public function make(): Command;
}
