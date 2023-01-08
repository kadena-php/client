<?php declare(strict_types=1);

namespace Kadena\Tests\Unit\Crypto;

use Kadena\Crypto\Hash;
use Kadena\Crypto\KeyFactory;
use Kadena\Crypto\MessageSigner;
use Kadena\ValueObjects\Signer\Signature;
use ParagonIE\ConstantTime\Base64UrlSafe;
use PHPUnit\Framework\TestCase;

final class MessageSignerTest extends TestCase
{
    /** @test */
    public function it_should_sign_a_message_with_a_key_pair_and_return_a_signed_message_object(): void
    {
        $message = 'test message';
        $keyPair = KeyFactory::generate();
        $signature = MessageSigner::sign($message, $keyPair);

        $expectedHash = Base64UrlSafe::encodeUnpadded(Hash::generic($message));

        $this->assertInstanceOf(Signature::class, $signature);
        $this->assertSame($expectedHash, $signature->hash);
        $this->assertTrue(MessageSigner::verifySignature(Hash::generic($message), $signature->signature, $keyPair->publicKey->key));
    }

    /** @test */
    public function it_should_sign_a_hash_with_a_key_pair_and_return_a_signed_message_object(): void
    {
        $message = 'test message';
        $keyPair = KeyFactory::generate();
        $hash = Hash::generic($message);
        $signature = MessageSigner::signHash($hash, $keyPair);

        $this->assertInstanceOf(Signature::class, $signature);
        $this->assertSame(Base64UrlSafe::encodeUnpadded($hash), $signature->hash);
        $this->assertTrue(MessageSigner::verifySignature(Hash::generic($message), $signature->signature, $keyPair->publicKey->key));
    }

    /** @test */
    public function it_should_verify_a_signature_with_a_public_key_and_message(): void
    {
        $keyPair = KeyFactory::generate();
        $message = 'test message';
        $signature = MessageSigner::sign($message, $keyPair);

        $this->assertTrue(MessageSigner::verifySignature(Hash::generic($message), $signature->signature, $keyPair->publicKey->key));
    }
}
