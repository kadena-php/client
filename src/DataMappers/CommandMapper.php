<?php declare(strict_types=1);

namespace Kadena\DataMappers;

use Carbon\Carbon;
use InvalidArgumentException;
use JsonException;
use Kadena\Contracts\DataMappers\CommandMapper as CommandMapperContract;
use Kadena\ValueObjects\Command\Command;
use Kadena\ValueObjects\Command\Metadata;
use Kadena\ValueObjects\Command\Payload\ContinuePayload;
use Kadena\ValueObjects\Command\Payload\ExecutePayload;
use Kadena\ValueObjects\Command\Payload\Payload;
use Kadena\ValueObjects\Command\Payload\PayloadType;
use Kadena\ValueObjects\Signer\Capability;
use Kadena\ValueObjects\Signer\CapabilityCollection;
use Kadena\ValueObjects\Signer\PublicKey;
use Kadena\ValueObjects\Signer\Signer;
use Kadena\ValueObjects\Signer\SignerCollection;
use ParagonIE\ConstantTime\Hex;
use ParagonIE\Halite\Alerts\InvalidKey;
use ParagonIE\Halite\Asymmetric\SignaturePublicKey;
use ParagonIE\HiddenString\HiddenString;

final class CommandMapper implements CommandMapperContract
{
    public static function toArray(Command $command): array
    {
        if ($command->payload->payloadType === PayloadType::EXECUTE) {
            $payload = [
                'data' => $command->payload->executePayload->data,
                'code' => $command->payload->executePayload->code,
            ];
        } else {
            $payload = [
                'data' => $command->payload->continuePayload->data,
                'step' => $command->payload->continuePayload->step,
                'rollback' => $command->payload->continuePayload->rollback,
                'pactId' => $command->payload->continuePayload->pactId,
                'proof' => $command->payload->continuePayload->proof,
            ];
        }

        $array = [
            'networkId' => $command->networkId,
            'payload' => [
                $command->payload->payloadType->value => $payload
            ],
            'meta' => [
                'creationTime' => $command->meta->creationTime->getTimestamp(),
                'ttl' => $command->meta->ttl,
                'gasLimit' => $command->meta->gasLimit,
                'chainId' => $command->meta->chainId,
                'gasPrice' => $command->meta->gasPrice,
                'sender' => $command->meta->sender,
            ],
            'nonce' => $command->nonce
        ];

        if ($command->signers->count() === 0) {
            return $array;
        }

        $signers = [];

        /** @var Signer $signer */
        foreach ($command->signers->toArray() as $signer) {
            $signerArray = [
                'pubKey' => $signer->publicKey->toString(),
            ];

            if ($signer->capabilities->count() !== 0) {
                $capabilityList = [];

                /** @var Capability $capability */
                foreach ($signer->capabilities->toArray() as $capability) {
                    $capabilityList[] = [
                        'name' => $capability->name,
                        'args' => $capability->arguments,
                    ];
                }

                $signerArray['clist'] = $capabilityList;
            }

            $signers[] = $signerArray;
        }

        $array['signers'] = $signers;

        return $array;
    }

    /**
     * @throws JsonException
     */
    public static function toString(Command $command): string
    {
        return json_encode(self::toArray($command), JSON_THROW_ON_ERROR);
    }

    /**
     * @throws JsonException
     * @throws InvalidKey
     */
    public static function fromString(string $commandJson): Command
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

        $signers = [];

        foreach ($command->signers as $signer) {
            $capabilities = [];

            if (isset($signer->clist)) {
                foreach ($signer->clist as $capabilityList) {
                    $capabilities[] = new Capability(
                        name: $capabilityList->name,
                        arguments: $capabilityList->args
                    );
                }
            }

            $signers[] = new Signer(
                publicKey: new PublicKey(new SignaturePublicKey(new HiddenString(Hex::decode($signer->pubKey)))),
                capabilities: new CapabilityCollection(...$capabilities)
            );
        }

        return new Command(
            meta: new Metadata(
                creationTime: Carbon::createFromTimestamp($meta->creationTime),
                ttl: $meta->ttl,
                gasLimit: $meta->gasLimit,
                chainId: $meta->chainId,
                gasPrice: $meta->gasPrice,
                sender: $meta->sender
            ),
            payload: $payload,
            networkId: $command->networkId ?? '0',
            nonce: $command->nonce ?? Carbon::now()->toISOString(),
            signers: new SignerCollection(...$signers)
        );
    }
}
