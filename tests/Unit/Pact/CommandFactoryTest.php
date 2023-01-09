<?php declare(strict_types=1);

namespace Kadena\Tests\Unit\Pact;

use Carbon\Carbon;
use Kadena\Crypto\KeyFactory;
use Kadena\Pact\CommandFactory;
use Kadena\Pact\MetadataFactory;
use Kadena\ValueObjects\Command\Command;
use Kadena\ValueObjects\Command\Payload\ContinuePayload;
use Kadena\ValueObjects\Command\Payload\ExecutePayload;
use Kadena\ValueObjects\Command\Payload\Payload;
use Kadena\ValueObjects\Command\Payload\PayloadType;
use Kadena\ValueObjects\Signer\Capability;
use Kadena\ValueObjects\Signer\CapabilityCollection;
use Kadena\ValueObjects\Signer\KeyPair;
use Kadena\ValueObjects\Signer\Signer;
use Kadena\ValueObjects\Signer\SignerCollection;
use PHPUnit\Framework\TestCase;

final class CommandFactoryTest extends TestCase
{
    private KeyPair $keyPair;

    public function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2021-11-26 12:30:00');

        $this->keyPair = KeyFactory::generate();
    }

    /** @test */
    public function it_should_be_able_to_construct_a_command_with_default_options_from_a_execute_payload(): void
    {
        $factory = new CommandFactory();
        $payload = new ExecutePayload(
            code: '(+ 2 2)'
        );

        $expected = new Command(
            meta: (new MetadataFactory())->make(),
            payload: new Payload(
                payloadType: PayloadType::EXECUTE,
                executePayload: $payload
            ),
            networkId: '0',
            nonce: Carbon::now()->toISOString(),
            signers: new SignerCollection(),
        );

        $actual = $factory->withExecutePayload($payload)->make();

        $this->assertEquals($expected, $actual);
    }

    /** @test */
    public function it_should_be_able_to_construct_a_command_with_default_options_from_a_continue_payload(): void
    {
        $factory = new CommandFactory();
        $payload = new ContinuePayload(
            pactId: 'pact-id',
            rollback: false,
            step: 0,
            proof: 'proof',
            data: []
        );

        $expected = new Command(
            meta: (new MetadataFactory())->make(),
            payload: new Payload(
                payloadType: PayloadType::CONTINUE,
                continuePayload: $payload
            ),
            networkId: '0',
            nonce: Carbon::now()->toISOString(),
            signers: new SignerCollection(),
        );

        $actual = $factory->withContinuePayload($payload)->make();

        $this->assertEquals($expected, $actual);
    }

    /** @test */
    public function it_should_be_able_to_construct_a_command_with_defined_options(): void
    {
        $factory = new CommandFactory();
        $payload = new ExecutePayload(
            code: '(+ 2 2)'
        );
        $signers = new SignerCollection(
            new Signer(
                publicKey: $this->keyPair->publicKey,
                capabilities: new CapabilityCollection(
                    new Capability(
                        name: 'test.cap',
                        arguments: []
                    )
                )
            )
        );
        $meta = (new MetadataFactory())->make();

        $expected = new Command(
            meta: $meta,
            payload: new Payload(
                payloadType: PayloadType::EXECUTE,
                executePayload: $payload
            ),
            networkId: 'network-0',
            nonce: 'test-nonce',
            signers: $signers,
        );

        $actual = $factory->withExecutePayload($payload)
            ->withNonce('test-nonce')
            ->withSigners($signers)
            ->withMetadata($meta)
            ->withNetworkId('network-0')
            ->make();

        $this->assertEquals($expected, $actual);
    }
}
