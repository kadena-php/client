<?php declare(strict_types=1);

namespace Kadena\Tests\Unit\Crypto;

use Kadena\Crypto\KeyFactory;
use Kadena\ValueObjects\Signer\KeyPair;
use Kadena\ValueObjects\Signer\PublicKey;
use Kadena\ValueObjects\Signer\SecretKey;
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
}
