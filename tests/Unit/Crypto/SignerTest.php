<?php declare(strict_types=1);

namespace Kadena\Tests\Unit\Crypto;

use Carbon\Carbon;
use Kadena\Crypto\Hash;
use Kadena\Crypto\KeyPair;
use Kadena\Crypto\Signature;
use Kadena\Crypto\Signer;
use Kadena\Pact\Command;
use Kadena\Pact\ExecutePayload;
use Kadena\Pact\Meta;
use Kadena\Pact\Payload;
use Kadena\Pact\PayloadType;
use ParagonIE\ConstantTime\Base64UrlSafe;
use PHPUnit\Framework\TestCase;

final class SignerTest extends TestCase
{
    /** @test */
    public function it_should_sign_a_message_with_a_key_pair_and_return_a_signed_message_object(): void
    {
        $message = 'test message';
        $keyPair = KeyPair::generate();
        $signature = Signer::sign($message, $keyPair);

        $expectedHash = Base64UrlSafe::encodeUnpadded(Hash::generic($message));

        $this->assertInstanceOf(Signature::class, $signature);
        $this->assertSame($expectedHash, $signature->hash);
        $this->assertTrue(Signer::verifySignature(Hash::generic($message), $signature->signature, $keyPair->publicKey));
    }

    /** @test */
    public function it_should_sign_a_hash_with_a_key_pair_and_return_a_signed_message_object(): void
    {
        $message = 'test message';
        $keyPair = KeyPair::generate();
        $hash = Hash::generic($message);
        $signature = Signer::signHash($hash, $keyPair);

        $this->assertInstanceOf(Signature::class, $signature);
        $this->assertSame(Base64UrlSafe::encodeUnpadded($hash), $signature->hash);
        $this->assertTrue(Signer::verifySignature(Hash::generic($message), $signature->signature, $keyPair->publicKey));
    }

    /** @test */
    public function it_should_verify_a_signature_with_a_public_key_and_message(): void
    {
        $keyPair = KeyPair::generate();
        $message = 'test message';
        $signature = Signer::sign($message, $keyPair);

        $this->assertTrue(Signer::verifySignature(Hash::generic($message), $signature->signature, $keyPair->publicKey));
    }

    /** @test */
    public function it_should_sign_a_command_with_a_key_pair_and_return_a_signature_object(): void
    {
        $keyPair = KeyPair::generate();

        $command = new Command(
            meta: new Meta(
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

        $signature = Signer::signCommand($command, $keyPair);

        $expectedHash = Base64UrlSafe::encodeUnpadded(Hash::generic($command->toString()));

        $this->assertInstanceOf(Signature::class, $signature);
        $this->assertSame($expectedHash, $signature->hash);
        $this->assertTrue(Signer::verifySignature(Hash::generic($command->toString()), $signature->signature, $keyPair->publicKey));
    }
}
