<?php declare(strict_types=1);

namespace Kadena\Tests\Unit\Pact;

use Carbon\Carbon;
use Kadena\Domain\Meta\Meta;
use PHPUnit\Framework\TestCase;

final class MetaTest extends TestCase
{
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
}
