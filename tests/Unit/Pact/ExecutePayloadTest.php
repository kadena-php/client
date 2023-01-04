<?php declare(strict_types=1);

namespace Kadena\Tests\Unit\Pact;

use Kadena\Pact\ExecutePayload;
use PHPUnit\Framework\TestCase;

final class ExecutePayloadTest extends TestCase
{
    /** @test */
    public function it_should_return_the_correct_array_representation_of_the_payload(): void
    {
        $code = 'test code';
        $data = ['key' => 'value'];

        $executePayload = new ExecutePayload($code, $data);
        $expectedArray = [
            'data' => $data,
            'code' => $code,
        ];

        $this->assertSame($expectedArray, $executePayload->toArray());
    }
}
