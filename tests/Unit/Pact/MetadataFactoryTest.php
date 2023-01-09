<?php declare(strict_types=1);

namespace Kadena\Tests\Unit\Pact;

use Carbon\Carbon;
use Kadena\Pact\MetadataFactory;
use Kadena\ValueObjects\Command\Metadata;
use PHPUnit\Framework\TestCase;

final class MetadataFactoryTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2021-11-26 12:30:00');
    }

    /** @test */
    public function it_should_be_able_to_construct_a_metadata_object_with_default_values(): void
    {
        $expected = new Metadata(
            creationTime: Carbon::now(),
            ttl: 7200,
            gasLimit: 10000,
            chainId: '0',
            gasPrice: 1e-8,
            sender: ''
        );

        $actual = (new MetadataFactory())->make();

        $this->assertEquals($expected, $actual);
    }

    /** @test */
    public function it_should_be_able_to_construct_a_metadata_object_with_supplied_options(): void
    {
        $expected = new Metadata(
            creationTime: Carbon::now()->subDay(),
            ttl: 3600,
            gasLimit: 15000,
            chainId: '2',
            gasPrice: 1e-7,
            sender: 'sender'
        );

        $actual = (new MetadataFactory())
            ->withOptions([
                'creationTime' => Carbon::now()->subDay()->getTimestamp(),
                'ttl' => 3600,
                'gasLimit' => 15000,
                'chainId' => '2',
                'gasPrice' => 1e-7,
                'sender' => 'sender',
            ])
            ->make();

        $this->assertEquals($expected, $actual);
    }
}
