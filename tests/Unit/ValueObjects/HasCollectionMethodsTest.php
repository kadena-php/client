<?php declare(strict_types=1);

namespace Kadena\Tests\Unit\ValueObjects;

use Carbon\Carbon;
use Kadena\Contracts\Collection;
use Kadena\ValueObjects\HasCollectionMethods;
use PHPUnit\Framework\TestCase;

final class HasCollectionMethodsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2021-11-26 12:30:00');
    }

    /** @test */
    public function it_should_be_able_to_cast_a_collection_to_an_array(): void
    {
        $stringArray = [
            'first',
            'second',
            'third',
            'fourth',
        ];

        $collection = new TestCollection(...$stringArray);

        $this->assertEquals($stringArray, $collection->toArray());
    }

    /** @test */
    public function it_should_be_able_to_return_the_first_element_of_a_collection(): void
    {
        $stringArray = [
            'first',
            'second',
            'third',
            'fourth',
        ];

        $collection = new TestCollection(...$stringArray);

        $this->assertEquals('first', $collection->first());
    }

    /** @test */
    public function it_should_be_able_to_return_a_collection_element_at_a_given_key(): void
    {
        $stringArray = [
            'first',
            'second',
            'third',
            'fourth',
        ];

        $collection = new TestCollection(...$stringArray);

        $this->assertEquals('third', $collection->get(2));
    }

    /** @test */
    public function it_should_be_able_to_return_the_int_count_of_the_amount_of_elements_in_the_collection(): void
    {
        $stringArray = [
            'first',
            'second',
            'third',
            'fourth',
        ];

        $collection = new TestCollection(...$stringArray);

        $this->assertEquals(4, $collection->count());
    }
}

final class TestCollection implements Collection
{
    use HasCollectionMethods;

    public function __construct(string ...$strings)
    {
        $this->array = $strings;
    }
}
