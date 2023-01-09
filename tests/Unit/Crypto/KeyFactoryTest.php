<?php declare(strict_types=1);

namespace Kadena\Tests\Unit\Crypto;

use Kadena\Crypto\KeyFactory;
use Kadena\ValueObjects\Signer\KeyPair;
use Kadena\ValueObjects\Signer\PublicKey;
use Kadena\ValueObjects\Signer\SecretKey;
use ParagonIE\ConstantTime\Hex;
use PHPUnit\Framework\TestCase;

final class KeyFactoryTest extends TestCase
{
    /** @test */
    public function it_should_generate_a_key_pair(): void
    {
        $keyPair = KeyFactory::generate();

        $this->assertInstanceOf(KeyPair::class, $keyPair);
        $this->assertInstanceOf(PublicKey::class, $keyPair->publicKey);
        $this->assertInstanceOf(SecretKey::class, $keyPair->secretKey);
    }

    /** @test */
    public function it_should_create_a_public_key_from_a_binary_string(): void
    {
        $keyPair = KeyFactory::generate();

        $binaryString = $keyPair->publicKey->key->getRawKeyMaterial();

        $actual = KeyFactory::publicKeyFromBytes($binaryString);

        $this->assertEquals($keyPair->publicKey->key->getRawKeyMaterial(), $actual->key->getRawKeyMaterial());
    }

    /** @test */
    public function it_should_create_a_public_key_from_a_hex_encoded_string(): void
    {
        $keyPair = KeyFactory::generate();

        $hexString = Hex::encode($keyPair->publicKey->key->getRawKeyMaterial());

        $actual = KeyFactory::publicKeyFromHex($hexString);

        $this->assertEquals($keyPair->publicKey->key->getRawKeyMaterial(), $actual->key->getRawKeyMaterial());
    }


    /** @test */
    public function it_should_create_a_secret_key_from_a_binary_string(): void
    {
        $keyPair = KeyFactory::generate();

        $binaryString = $keyPair->secretKey->key->getRawKeyMaterial();

        $actual = KeyFactory::secretKeyFromBytes($binaryString);

        $this->assertEquals($keyPair->secretKey->key->getRawKeyMaterial(), $actual->key->getRawKeyMaterial());
    }

    /** @test */
    public function it_should_create_a_secret_key_from_a_hex_encoded_string(): void
    {
        $keyPair = KeyFactory::generate();

        $hexString = Hex::encode($keyPair->secretKey->key->getRawKeyMaterial());

        $actual = KeyFactory::secretKeyFromHex($hexString);

        $this->assertEquals($keyPair->secretKey->key->getRawKeyMaterial(), $actual->key->getRawKeyMaterial());
    }
}
