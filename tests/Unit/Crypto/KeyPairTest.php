<?php declare(strict_types=1);

namespace Kadena\Tests\Unit\Crypto;

use Kadena\Crypto\KeyPair;
use ParagonIE\Halite\Asymmetric\SignaturePublicKey;
use ParagonIE\Halite\Asymmetric\SignatureSecretKey;
use PHPUnit\Framework\TestCase;

final class KeyPairTest extends TestCase
{
    /** @test */
    public function it_should_generate_a_key_pair(): void
    {
        $keyPair = KeyPair::generate();

        $this->assertInstanceOf(KeyPair::class, $keyPair);
        $this->assertInstanceOf(SignaturePublicKey::class, $keyPair->publicKey);
        $this->assertInstanceOf(SignatureSecretKey::class, $keyPair->secretKey);
    }
}
