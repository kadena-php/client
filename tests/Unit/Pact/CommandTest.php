<?php declare(strict_types=1);

namespace Kadena\Tests\Unit\Pact;

use Carbon\Carbon;
use Kadena\Crypto\Hash;
use Kadena\Crypto\MessageSigner;
use Kadena\ValueObjects\Command\Command;
use Kadena\ValueObjects\Command\Metadata;
use Kadena\ValueObjects\Command\Payload\ExecutePayload;
use Kadena\ValueObjects\Command\Payload\Payload;
use Kadena\ValueObjects\Command\Payload\PayloadType;
use Kadena\ValueObjects\Command\SignedCommand;
use Kadena\ValueObjects\Signer\KeyPair;
use Kadena\ValueObjects\Signer\KeyPairCollection;
use ParagonIE\ConstantTime\Base64UrlSafe;
use ParagonIE\ConstantTime\Hex;
use PHPUnit\Framework\TestCase;

final class CommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2021-11-26 12:30:00');
    }

    /** @test */
    public function it_should_set_nonce_to_current_time_if_not_defined(): void
    {
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
            )
        );

        $this->assertEquals(Carbon::now()->toISOString(), $command->nonce);
    }

    /** @test */
    public function it_should_cast_to_array(): void
    {
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
            )
        );

        $expected = [
            'signers' => [],
            'networkId' => null,
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
            'nonce' => '2021-11-26T12:30:00.000000Z'
        ];

        $this->assertEquals($expected, $command->toArray());
    }

    /** @test */
    public function it_should_cast_to_string(): void
    {
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
            )
        );

        $expected = json_encode([
            'signers' => [],
            'networkId' => null,
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
            'nonce' => '2021-11-26T12:30:00.000000Z'
        ], JSON_THROW_ON_ERROR);

        $this->assertEquals($expected, $command->toString());
    }

    /** @test */
    public function it_should_construct_from_string(): void
    {
        $expected = new Command(
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
            )
        );

        $jsonCommand = json_encode([
            'signers' => [],
            'networkId' => null,
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
            'nonce' => '2021-11-26T12:30:00.000000Z'
        ], JSON_THROW_ON_ERROR);

        $this->assertEquals($expected, Command::fromString($jsonCommand));
    }

    /** @test */
    public function it_should_be_able_to_set_signers(): void
    {
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
            )
        );

        $expectedWithoutSigners = [
            'signers' => [],
            'networkId' => null,
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
            'nonce' => '2021-11-26T12:30:00.000000Z'
        ];

        $this->assertEquals($expectedWithoutSigners, $command->toArray());

        $expectedWithSigners = [
            'signers' => [
                ['pubKey' => 'public-key1'],
                ['pubKey' => 'public-key2'],
            ],
            'networkId' => null,
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
            'nonce' => '2021-11-26T12:30:00.000000Z'
        ];

        $command->setSigners(['public-key1', 'public-key2']);

        $this->assertEquals($expectedWithSigners, $command->toArray());
    }

    /** @test */
    public function it_should_return_a_signed_command_object_when_using_get_signed_command(): void
    {
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
            )
        );

        $keyPair = KeyPair::generate();

        $command->setSigners([Hex::encode($keyPair->publicKey->getRawKeyMaterial())]);

        $message = $command->toString();
        $expectedHash = Base64UrlSafe::encodeUnpadded(Hash::generic($message));

        $signedCommand = $command->getSignedCommand(new KeyPairCollection($keyPair));

        $this->assertInstanceOf(SignedCommand::class, $signedCommand);
        $this->assertSame($expectedHash, $signedCommand->hash);
        $this->assertTrue(MessageSigner::verifySignature(Hash::generic($message), $signedCommand->signatures->first()->signature, $keyPair->publicKey));
    }
}
