<?php declare(strict_types=1);

namespace Kadena\Tests\Unit\Pact;

use Carbon\Carbon;
use Kadena\Pact\Meta;
use PHPUnit\Framework\TestCase;

final class MetaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2021-11-26 12:30:00');
    }

    /** @test */
    public function it_should_return_the_correct_array_representation_of_the_meta_object(): void
    {
        $creationTime = Carbon::now();
        $ttl = 300;
        $gasLimit = 50;
        $chainId = 'test-chain-id';
        $gasPrice = 1.25;
        $sender = 'test-sender';

        $meta = new Meta($creationTime, $ttl, $gasLimit, $chainId, $gasPrice, $sender);
        $expectedArray = [
            'creationTime' => $creationTime->getTimestamp(),
            'ttl' => $ttl,
            'gasLimit' => $gasLimit,
            'chainId' => $chainId,
            'gasPrice' => $gasPrice,
            'sender' => $sender
        ];

        $this->assertSame($expectedArray, $meta->toArray());
    }

    /** @test */
    public function it_should_be_able_to_be_constructed_from_an_options_array(): void
    {
        $creationTime = Carbon::now();
        $ttl = 300;
        $gasLimit = 50;
        $chainId = 'test-chain-id';
        $gasPrice = 1.25;
        $sender = 'test-sender';

        $expected = new Meta($creationTime, $ttl, $gasLimit, $chainId, $gasPrice, $sender);

        $options = [
            'creationTime' => $creationTime->getTimestamp(),
            'ttl' => $ttl,
            'gasLimit' => $gasLimit,
            'chainId' => $chainId,
            'gasPrice' => $gasPrice,
            'sender' => $sender
        ];

        $actual = Meta::create($options);

        $this->assertEquals($expected, $actual);
    }

    /** @test */
    public function it_should_be_able_to_be_constructed_using_the_create_method_with_default_options(): void
    {
        $creationTime = Carbon::now();
        $ttl = 7200;
        $gasLimit = 10000;
        $chainId = '0';
        $gasPrice = 1e-8;
        $sender = '';

        $expected = new Meta($creationTime, $ttl, $gasLimit, $chainId, $gasPrice, $sender);

        $actual = Meta::create();

        $this->assertEquals($expected, $actual);
    }
}
