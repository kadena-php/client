<?php declare(strict_types=1);

namespace Kadena\Tests\Unit\Crypto;

use Kadena\Domain\Signature\Signature;
use Kadena\Domain\Signature\SignatureCollection;
use PHPUnit\Framework\TestCase;

final class SignatureCollectionTest extends TestCase
{
    /** @test */
    public function it_should_return_the_first_signature(): void
    {
        $signature1 = new Signature('hash', 'sig1');
        $signature2 = new Signature('hash', 'sig2');
        $signatureCollection = new SignatureCollection($signature1, $signature2);

        $this->assertSame($signature1, $signatureCollection->first());
    }

    /** @test */
    public function it_should_return_all_signatures(): void
    {
        $signature1 = new Signature('hash', 'sig1');
        $signature2 = new Signature('hash', 'sig2');
        $signatureCollection = new SignatureCollection($signature1, $signature2);

        $this->assertEquals([$signature1, $signature2], $signatureCollection->toArray());
    }

    /** @test */
    public function it_should_create_a_new_signature_collection_from_an_array(): void
    {
        $signatures = [
            ['sig' => 'sig1'],
            ['sig' => 'sig2']
        ];

        $signatureCollection = SignatureCollection::fromArray($signatures, 'hash');

        $this->assertInstanceOf(SignatureCollection::class, $signatureCollection);
        $this->assertSame('hash', $signatureCollection->first()->hash);
        $this->assertSame('sig1', $signatureCollection->first()->signature);
    }
}
