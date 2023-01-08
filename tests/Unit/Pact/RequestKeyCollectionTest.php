<?php declare(strict_types=1);

namespace Kadena\Tests\Unit\Pact;

use Kadena\ValueObjects\RequestKey\RequestKey;
use Kadena\ValueObjects\RequestKey\RequestKeyCollection;
use PHPUnit\Framework\TestCase;

final class RequestKeyCollectionTest extends TestCase
{
    /** @test */
    public function it_should_create_request_key_collection(): void
    {
        $requestKey1 = new RequestKey('key1');
        $requestKey2 = new RequestKey('key2');

        $requestKeyCollection = new RequestKeyCollection($requestKey1, $requestKey2);

        $this->assertEquals([$requestKey1, $requestKey2], $requestKeyCollection->toArray());
    }

    /** @test */
    public function it_should_get_first_request_key(): void
    {
        $requestKey1 = new RequestKey('key1');
        $requestKey2 = new RequestKey('key2');

        $requestKeyCollection = new RequestKeyCollection($requestKey1, $requestKey2);

        $this->assertEquals($requestKey1, $requestKeyCollection->first());
    }

    /** @test */
    public function it_should_convert_to_plain_array(): void
    {
        $requestKey1 = new RequestKey('key1');
        $requestKey2 = new RequestKey('key2');

        $requestKeyCollection = new RequestKeyCollection($requestKey1, $requestKey2);

        $this->assertEquals(['key1', 'key2'], $requestKeyCollection->toPlainArray());
    }
}
