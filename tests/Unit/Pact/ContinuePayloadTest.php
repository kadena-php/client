<?php declare(strict_types=1);

namespace Kadena\Tests\Unit\Pact;

use Kadena\Pact\ContinuePayload;
use PHPUnit\Framework\TestCase;

final class ContinuePayloadTest extends TestCase
{
    /** @test */
    public function it_should_return_the_correct_array_representation_of_the_payload(): void
    {
        $pactId = 'pact-id';
        $rollback = false;
        $step = 0;
        $proof = 'proof';
        $data = ['key' => 'value'];

        $continuePayload = new ContinuePayload($pactId, $rollback, $step, $proof, $data);

        $expectedArray = [
            'proof' => $proof,
            'pactId' => $pactId,
            'rollback' => $rollback,
            'step' => $step,
            'data' => $data
        ];

        $this->assertSame($expectedArray, $continuePayload->toArray());
    }
}
