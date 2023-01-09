<?php declare(strict_types=1);

namespace Kadena\Pact;

use Carbon\Carbon;
use Kadena\Contracts\Pact\CommandFactory as CommandFactoryContract;
use Kadena\Exceptions\MissingPayloadException;
use Kadena\ValueObjects\Command\Command;
use Kadena\ValueObjects\Command\Metadata;
use Kadena\ValueObjects\Command\Payload\ContinuePayload;
use Kadena\ValueObjects\Command\Payload\ExecutePayload;
use Kadena\ValueObjects\Command\Payload\Payload;
use Kadena\ValueObjects\Command\Payload\PayloadType;
use Kadena\ValueObjects\Signer\Signer;
use Kadena\ValueObjects\Signer\SignerCollection;

final class CommandFactory implements CommandFactoryContract
{
    private Metadata $metadata;
    private Payload $payload;
    private string $networkId;
    private string $nonce;
    private SignerCollection $signers;

    public static function load(Command $command): self
    {
        $self = new self();

        if ($command->payload->payloadType === PayloadType::EXECUTE) {
            $self->withExecutePayload($command->payload->executePayload);
        }

        if ($command->payload->payloadType === PayloadType::CONTINUE) {
            $self->withContinuePayload($command->payload->continuePayload);
        }

        return $self->withSigners($command->signers)
            ->withNetworkId($command->networkId)
            ->withNonce($command->nonce)
            ->withMetadata($command->meta);
    }

    public function addSigner(Signer $signer): self
    {
        if (! isset($this->signers)) {
            $this->signers = new SignerCollection();
        }

        $signers = $this->signers->toArray();
        $signers[] = $signer;

        $this->signers = new SignerCollection(...$signers);
    }

    public function withMetadata(Metadata $metadata): self
    {
        $this->metadata = $metadata;

        return $this;
    }

    public function withExecutePayload(ExecutePayload $payload): self
    {
        $this->payload = new Payload(
            payloadType: PayloadType::EXECUTE,
            executePayload: $payload
        );

        return $this;
    }

    public function withContinuePayload(ContinuePayload $payload): self
    {
        $this->payload = new Payload(
            payloadType: PayloadType::CONTINUE,
            continuePayload: $payload
        );

        return $this;
    }

    public function withNonce(string $nonce): self
    {
        $this->nonce = $nonce;

        return $this;
    }

    public function withNetworkId(string $networkId): self
    {
        $this->networkId = $networkId;

        return $this;
    }

    public function withSigners(SignerCollection $signers): self
    {
        $this->signers = $signers;

        return $this;
    }

    /**
     * @throws MissingPayloadException
     */
    public function make(): Command
    {
        if (! isset($this->payload)) {
            throw new MissingPayloadException('Payload is required to build command');
        }

        if (! isset($this->metadata)) {
            $this->metadata = (new MetadataFactory())->make();
        }

        if (! isset($this->nonce)) {
            $this->nonce = Carbon::now()->toISOString();
        }

        if (! isset($this->networkId)) {
            $this->networkId = '0';
        }

        if (! isset($this->signers)) {
            $this->signers = new SignerCollection();
        }

        return new Command(
            meta: $this->metadata,
            payload: $this->payload,
            networkId: $this->networkId,
            nonce: $this->nonce,
            signers: $this->signers
        );
    }
}
