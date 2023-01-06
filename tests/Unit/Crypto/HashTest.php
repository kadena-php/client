<?php declare(strict_types=1);

namespace Kadena\Tests\Unit\Crypto;

use Kadena\Domain\Crypto\Hash;
use PHPUnit\Framework\TestCase;

final class HashTest extends TestCase
{
    /** @test */
    public function it_should_hash_a_string_using_sodium_generic(): void
    {
        $input = 'string';

        $expected = sodium_crypto_generichash($input);
        $actual = Hash::generic($input);

        $this->assertEquals($expected, $actual);
    }
}
