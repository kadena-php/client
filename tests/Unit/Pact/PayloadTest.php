<?php declare(strict_types=1);

namespace Kadena\Tests\Unit\Pact;

use InvalidArgumentException;
use Kadena\Domain\Payload\ContinuePayload;
use Kadena\Domain\Payload\ExecutePayload;
use Kadena\Domain\Payload\Payload;
use Kadena\Domain\Payload\PayloadType;
use PHPUnit\Framework\TestCase;

final class PayloadTest extends TestCase
{
    /** @test */
    public function it_should_validate_continue_payload(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Payload(PayloadType::CONTINUE, null, new ExecutePayload('test', []));
    }

    /** @test */
    public function it_should_validate_execute_payload(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Payload(PayloadType::EXECUTE, new ContinuePayload('test', true, 1), null);
    }

    /** @test */
    public function it_should_create_and_return_valid_execute_payload(): void
    {
        $payload = new Payload(
            payloadType: PayloadType::EXECUTE,
            executePayload: new ExecutePayload(
                code: 'test',
                data: ['key' => 'value']
            )
        );

        $this->assertSame([PayloadType::EXECUTE->value => ['data' => ['key' => 'value'], 'code' => 'test']], $payload->toArray());
    }

    /** @test */
    public function it_should_create_and_return_valid_continue_payload(): void
    {
        $payload = new Payload(
            payloadType: PayloadType::CONTINUE,
            continuePayload: new ContinuePayload(
                pactId: 'test',
                rollback: true,
                step: 1,
                proof: 'proof',
                data: ['key' => 'value']
            )
        );

        $this->assertSame([PayloadType::CONTINUE->value => ['proof' => 'proof', 'pactId' => 'test', 'rollback' => true, 'step' => 1, 'data' => ['key' => 'value']]], $payload->toArray());
    }
}
