<?php declare(strict_types=1);

namespace Kadena\Tests\Unit\Crypto;

use Kadena\Crypto\CommandSigner;
use Kadena\Crypto\Hash;
use Kadena\Crypto\KeyFactory;
use Kadena\Crypto\MessageSigner;
use Kadena\DataMappers\CommandMapper;
use Kadena\Pact\CommandFactory;
use Kadena\ValueObjects\Command\Payload\ExecutePayload;
use Kadena\ValueObjects\Command\SignedCommand;
use Kadena\ValueObjects\Signer\KeyPairCollection;
use ParagonIE\ConstantTime\Base64UrlSafe;
use PHPUnit\Framework\TestCase;

final class CommandSignerTest extends TestCase
{
    /** @test */
    public function it_should_sign_a_command_with_a_key_pair_and_return_a_signature_object(): void
    {
        $keyPair = KeyFactory::generate();

        $command = (new CommandFactory())->withExecutePayload(new ExecutePayload(
            code: '(+ 2 2)'
        ))->make();

        $signed = CommandSigner::sign($command, new KeyPairCollection($keyPair));

        $expectedHash = Base64UrlSafe::encodeUnpadded(Hash::generic(CommandMapper::toString($command)));

        $this->assertInstanceOf(SignedCommand::class, $signed);
        $this->assertSame($expectedHash, $signed->hash);
        $this->assertTrue(MessageSigner::verifySignature(Hash::generic(CommandMapper::toString($command)), $signed->signatures->first()->signature, $keyPair->publicKey->key));
    }
}
