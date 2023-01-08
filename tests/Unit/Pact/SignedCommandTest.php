<?php declare(strict_types=1);

namespace Kadena\Tests\Unit\Pact;

use Carbon\Carbon;
use InvalidArgumentException;
use Kadena\ValueObjects\Command\Command;
use Kadena\ValueObjects\Command\Metadata;
use Kadena\ValueObjects\Command\Payload\ExecutePayload;
use Kadena\ValueObjects\Command\Payload\Payload;
use Kadena\ValueObjects\Command\Payload\PayloadType;
use Kadena\ValueObjects\Command\SignedCommand;
use Kadena\ValueObjects\Signer\Signature;
use Kadena\ValueObjects\Signer\SignatureCollection;
use PHPUnit\Framework\TestCase;

final class SignedCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2021-11-26 12:30:00');
    }

    /** @test */
    public function it_should_create_signed_command_from_string(): void
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

        $command->setSigners(['public-key']);

        $signature = new Signature(
            hash: 'hash',
            signature: 'signature',
            publicKey: 'public-key',
        );

        $signatures = new SignatureCollection($signature);

        $signedCommand = new SignedCommand('hash', $signatures, $command);

        $commandJson = $signedCommand->toString();

        $signedCommandFromJson = SignedCommand::fromString($commandJson);

        $this->assertEquals($signedCommand->command, $signedCommandFromJson->command);
        $this->assertEquals($signedCommand->hash, $signedCommandFromJson->hash);
    }

    /** @test */
    public function it_should_validate_signatures_when_creating(): void
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

        $command->setSigners(['public-key']);

        $signature1 = new Signature(
            hash: 'hash',
            signature: 'signature',
            publicKey: 'public-key',
        );

        $signature2 = new Signature(
            hash: 'incorrect',
            signature: 'signature',
            publicKey: 'public-key',
        );

        $signatures = new SignatureCollection($signature1, $signature2);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Signatures for different hashes found: incorrect, expected: hash');

        new SignedCommand('hash', $signatures, $command);
    }

    /** @test */
    public function it_should_return_the_expected_array(): void
    {
        $signature1 = new Signature('hash1', 'signature1');
        $signature2 = new Signature('hash1', 'signature2');
        $signatureCollection = new SignatureCollection($signature1, $signature2);

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

        $signedCommand = new SignedCommand('hash1', $signatureCollection, $command);

        $expectedArray = [
            'hash' => 'hash1',
            'sigs' => [
                ['sig' => 'signature1'],
                ['sig' => 'signature2'],
            ],
            'cmd' => $command->toString(),
        ];

        $this->assertSame($expectedArray, $signedCommand->toArray());
    }

    /** @test */
    public function it_should_return_the_expected_string(): void
    {
        $signature1 = new Signature('hash1', 'signature1');
        $signature2 = new Signature('hash1', 'signature2');
        $signatureCollection = new SignatureCollection($signature1, $signature2);

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

        $signedCommand = new SignedCommand('hash1', $signatureCollection, $command);

        $expectedString = '{"hash":"hash1","sigs":[{"sig":"signature1"},{"sig":"signature2"}],"cmd":"{\"signers\":[],\"networkId\":null,\"payload\":{\"exec\":{\"data\":[],\"code\":\"(+ 2 2)\"}},\"meta\":{\"creationTime\":0,\"ttl\":0,\"gasLimit\":0,\"chainId\":\"\",\"gasPrice\":0,\"sender\":\"\"},\"nonce\":\"2021-11-26T12:30:00.000000Z\"}"}';
        $this->assertSame($expectedString, $signedCommand->toString());
    }
}
