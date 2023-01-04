<?php declare(strict_types=1);

namespace Kadena\Pact;

use Carbon\Carbon;
use InvalidArgumentException;
use JsonException;
use Kadena\Crypto\KeyPair;
use Kadena\Crypto\KeyPairCollection;
use Kadena\Crypto\SignatureCollection;
use Kadena\Crypto\Signer;
use ParagonIE\ConstantTime\Hex;
use ParagonIE\Halite\Alerts\InvalidType;
use SodiumException;

final class Command
{
    public readonly string $nonce;
    private array $signers = [];

    public function __construct(
        public readonly Meta $meta,
        public readonly Payload $payload,
        public readonly ?string $networkId = null,
        ?string $nonce = null,
    ) {
        if ($nonce === null) {
            $this->nonce = Carbon::now()->toISOString();
        } else {
            $this->nonce = $nonce;
        }
    }

    /**
     * @throws JsonException
     */
    public function toString(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
    }

    public function toArray(): array
    {
        return [
            'signers' => $this->signers,
            'networkId' => $this->networkId,
            'payload' => $this->payload->toArray(),
            'meta' => $this->meta->toArray(),
            'nonce' => $this->nonce,
        ];
    }

    /**
     * @throws InvalidType
     * @throws SodiumException
     */
    public function getSignedCommand(KeyPairCollection $keyPairCollection): SignedCommand
    {
        $this->setSigners(array_map([$this, 'getSigners'], $keyPairCollection->toArray()));

        $signatures = [];

        foreach ($keyPairCollection->toArray() as $keyPair) {
            $signatures[] = Signer::signCommand($this, $keyPair);
        }

        $signatureCollection = new SignatureCollection(...$signatures);

        return new SignedCommand(
            hash: $signatureCollection->first()->hash,
            signatures: $signatureCollection,
            command: $this
        );
    }

    private function getSigners(KeyPair $keyPair): string
    {
        return Hex::encode($keyPair->publicKey->getRawKeyMaterial());
    }

    public function setSigners(array $signers): void
    {
        $pubKeys = [];

        foreach ($signers as $signer) {
            $pubKeys[] = ['pubKey' => $signer];
        }

        $this->signers = $pubKeys;
    }

    public static function fromString(string $commandJson): self
    {
        $command = json_decode($commandJson, false, 512, JSON_THROW_ON_ERROR);

        if (! isset($command->signers, $command->payload, $command->meta)) {
            throw new InvalidArgumentException('Invalid Command JSON string given');
        }

        $meta = $command->meta;

        if (! isset(
            $meta->creationTime,
            $meta->ttl,
            $meta->gasLimit,
            $meta->chainId,
            $meta->gasPrice,
            $meta->sender,
        )) {
            throw new InvalidArgumentException('Invalid meta object given');
        }

        $payloadType = PayloadType::from(key((array) $command->payload));

        if ($payloadType === PayloadType::EXECUTE) {
            $exec = $command->payload->exec;

            if (! isset($exec->data, $exec->code)) {
                throw new InvalidArgumentException('Invalid execute command object given');
            }

            $payload = new Payload(
                payloadType: $payloadType,
                executePayload: new ExecutePayload(
                    code: $exec->code,
                    data: (array) ($exec->data ?? []),
                )
            );
        } elseif ($payloadType === PayloadType::CONTINUE) {
            $cont = $command->payload->cont;

            if (! isset($cont->data, $cont->code, $cont->pactId, $cont->rollback, $cont->step)) {
                throw new InvalidArgumentException('Invalid continue command object given');
            }

            $payload = new Payload(
                payloadType: $payloadType,
                continuePayload: new ContinuePayload(
                    pactId: $cont->pactId,
                    rollback: $cont->rollback,
                    step: $cont->step,
                    proof: $cont->proof ?? null,
                    data: (array) ($cont->data ?? []),
                )
            );
        } else {
            throw new InvalidArgumentException('Unknown payload type');
        }

        $commandObject = new self(
            meta: new Meta(
                creationTime: Carbon::createFromTimestamp($meta->creationTime),
                ttl: $meta->ttl,
                gasLimit: $meta->gasLimit,
                chainId: $meta->chainId,
                gasPrice: $meta->gasPrice,
                sender: $meta->sender
            ),
            payload: $payload,
            networkId: $command->networkId ?? null,
            nonce: $command->nonce ?? null
        );

        $commandObject->setSigners(array_map(static function (array|object $signer) {
            $signer = (array) $signer;

            return $signer['pubKey'];
        }, $command->signers));

        return $commandObject;
    }
}
