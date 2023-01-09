<?php declare(strict_types=1);

namespace Kadena\Tests\Unit\DataMappers;

use Carbon\Carbon;
use Kadena\Crypto\KeyFactory;
use Kadena\DataMappers\SignedCommandMapper;
use Kadena\ValueObjects\Command\Command;
use Kadena\ValueObjects\Command\Metadata;
use Kadena\ValueObjects\Command\Payload\ExecutePayload;
use Kadena\ValueObjects\Command\Payload\Payload;
use Kadena\ValueObjects\Command\Payload\PayloadType;
use Kadena\ValueObjects\Command\SignedCommand;
use Kadena\ValueObjects\Signer\Capability;
use Kadena\ValueObjects\Signer\CapabilityCollection;
use Kadena\ValueObjects\Signer\Signature;
use Kadena\ValueObjects\Signer\SignatureCollection;
use Kadena\ValueObjects\Signer\Signer;
use Kadena\ValueObjects\Signer\SignerCollection;
use PHPUnit\Framework\TestCase;

final class SignedCommandMapperTest extends TestCase
{
    private SignedCommand $signedCommand;
    private array $signedCommandArray;

    public function setUp(): void
    {
        parent::setUp();

        $keyPair = KeyFactory::generate();

        $command = new Command(
            meta: new Metadata(
                creationTime: Carbon::createFromTimestamp(0),
                ttl: 0,
                gasLimit: 0,
                chainId: '',
                gasPrice: 0,
                sender: ''
            ),
            payload: new Payload(
                payloadType: PayloadType::EXECUTE,
                executePayload: new ExecutePayload(
                    code: '(+ 2 2)'
                )
            ),
            networkId: 'testnet0',
            nonce: 'test',
            signers: new SignerCollection(new Signer(
                publicKey: $keyPair->publicKey,
                capabilities: new CapabilityCollection(
                    new Capability(
                        name: 'cap.example',
                        arguments: []
                    )
                )
            ))
        );

        $commandArray = [
            'networkId' => 'testnet0',
            'payload' => [
                'exec' => [
                    'data' => [],
                    'code' => '(+ 2 2)'
                ]
            ],
            'meta' => [
                'creationTime' => 0,
                'ttl' => 0,
                'gasLimit' => 0,
                'chainId' => '',
                'gasPrice' => 0,
                'sender' => ''
            ],
            'nonce' => 'test',
            'signers' => [
                [
                    'pubKey' => $keyPair->publicKey->toString(),
                    'clist' => [
                        [
                            'name' => 'cap.example',
                            'args' => []
                        ]
                    ]
                ]
            ],
        ];

        $this->signedCommand = new SignedCommand(
            hash: 'test-hash',
            signatures: new SignatureCollection(new Signature(
                hash: 'test-hash',
                signature: 'test-signature'
            )),
            command: $command
        );

        $this->signedCommandArray = [
            'cmd' => json_encode($commandArray, JSON_THROW_ON_ERROR),
            'hash' => 'test-hash',
            'sigs' => [
                ['sig' => 'test-signature']
            ],
        ];
    }

    /** @test */
    public function it_should_be_able_to_map_a_signed_command_to_an_array(): void
    {
        $actual = SignedCommandMapper::toArray($this->signedCommand);

        $this->assertEquals($this->signedCommandArray, $actual);
    }

    /** @test */
    public function it_should_be_able_to_map_a_signed_command_to_a_string(): void
    {
        $actual = SignedCommandMapper::toString($this->signedCommand);

        $this->assertEquals(json_encode($this->signedCommandArray, JSON_THROW_ON_ERROR), $actual);
    }

    /** @test */
    public function it_should_be_able_to_map_a_string_to_a_signed_command(): void
    {
        $actual = SignedCommandMapper::fromString(json_encode($this->signedCommandArray, JSON_THROW_ON_ERROR));

        $this->assertEquals($this->signedCommand, $actual);
    }
}
