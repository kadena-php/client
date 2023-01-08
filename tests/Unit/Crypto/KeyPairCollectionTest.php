<?php declare(strict_types=1);

namespace Kadena\Tests\Unit\Crypto;

use Kadena\ValueObjects\Signer\KeyPair;
use Kadena\ValueObjects\Signer\KeyPairCollection;
use PHPUnit\Framework\TestCase;

final class KeyPairCollectionTest extends TestCase
{
    /** @test */
    public function it_should_cast_to_an_array(): void
    {
        $keyPair1 = KeyPair::generate();
        $keyPair2 = KeyPair::generate();
        $keyPairCollection = new KeyPairCollection($keyPair1, $keyPair2);

        $this->assertEquals([$keyPair1, $keyPair2], $keyPairCollection->toArray());
    }
}
