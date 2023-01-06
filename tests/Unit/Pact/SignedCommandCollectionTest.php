<?php declare(strict_types=1);

namespace Kadena\Tests\Unit\Pact;

use Carbon\Carbon;
use Kadena\Application\Command;
use Kadena\Domain\Command\SignedCommand;
use Kadena\Domain\Command\SignedCommandCollection;
use Kadena\Domain\Meta\Meta;
use Kadena\Domain\Payload\ExecutePayload;
use Kadena\Domain\Payload\Payload;
use Kadena\Domain\Payload\PayloadType;
use Kadena\Domain\Signature\Signature;
use Kadena\Domain\Signature\SignatureCollection;
use PHPUnit\Framework\TestCase;

final class SignedCommandCollectionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2021-11-26 12:30:00');
    }

    /** @test */
    public function it_should_convert_to_an_array(): void
    {
        $command = new Command(
            meta: new Meta(
                creationTime: Carbon::createFromTimestamp(0),
                ttl: 0,
                gasLimit: 0,
                chainId: '',
                gasPrice: 0,
                sender: ''
            ),
            payload: new Payload(
                payloadType: PayloadType::EXECUTE,
                executePayload: new ExecutePayload(
                    code: '(+ 2 2)'
                )
            )
        );

        $command->setSigners(['public-key']);

        $signature = new Signature(
            hash: 'hash',
            signature: 'signature',
            publicKey: 'public-key',
        );

        $signatures = new SignatureCollection($signature);

        $signedCommand = new SignedCommand('hash', $signatures, $command);

        $signedCommandCollection = new SignedCommandCollection($signedCommand);

        $this->assertEquals([$signedCommand], $signedCommandCollection->toArray());
    }

    /** @test */
    public function it_should_convert_in_to_a_payload_array(): void
    {
        $command = new Command(
            meta: new Meta(
                creationTime: Carbon::createFromTimestamp(0),
                ttl: 0,
                gasLimit: 0,
                chainId: '',
                gasPrice: 0,
                sender: ''
            ),
            payload: new Payload(
                payloadType: PayloadType::EXECUTE,
                executePayload: new ExecutePayload(
                    code: '(+ 2 2)'
                )
            )
        );

        $command->setSigners(['public-key']);

        $signature = new Signature(
            hash: 'hash',
            signature: 'signature',
            publicKey: 'public-key',
        );

        $signatures = new SignatureCollection($signature);

        $signedCommand = new SignedCommand('hash', $signatures, $command);

        $signedCommandCollection = new SignedCommandCollection($signedCommand);

        $expected = [
            'cmds' => [
                [
                    'hash' => 'hash',
                    'sigs' => [
                        [
                            'sig' => 'signature'
                        ]
                    ],
                    'cmd' => '{"signers":[{"pubKey":"public-key"}],"networkId":null,"payload":{"exec":{"data":[],"code":"(+ 2 2)"}},"meta":{"creationTime":0,"ttl":0,"gasLimit":0,"chainId":"","gasPrice":0,"sender":""},"nonce":"2021-11-26T12:30:00.000000Z"}'
                ]
            ]
        ];

        $this->assertEquals($expected, $signedCommandCollection->toPayload());
    }

    /** @test */
    public function it_should_convert_in_to_a_json_payload_string(): void
    {
        $command = new Command(
            meta: new Meta(
                creationTime: Carbon::createFromTimestamp(0),
                ttl: 0,
                gasLimit: 0,
                chainId: '',
                gasPrice: 0,
                sender: ''
            ),
            payload: new Payload(
                payloadType: PayloadType::EXECUTE,
                executePayload: new ExecutePayload(
                    code: '(+ 2 2)'
                )
            )
        );

        $command->setSigners(['public-key']);

        $signature = new Signature(
            hash: 'hash',
            signature: 'signature',
            publicKey: 'public-key',
        );

        $signatures = new SignatureCollection($signature);

        $signedCommand = new SignedCommand('hash', $signatures, $command);

        $signedCommandCollection = new SignedCommandCollection($signedCommand);

        $expected = '{"cmds":[{"hash":"hash","sigs":[{"sig":"signature"}],"cmd":"{\"signers\":[{\"pubKey\":\"public-key\"}],\"networkId\":null,\"payload\":{\"exec\":{\"data\":[],\"code\":\"(+ 2 2)\"}},\"meta\":{\"creationTime\":0,\"ttl\":0,\"gasLimit\":0,\"chainId\":\"\",\"gasPrice\":0,\"sender\":\"\"},\"nonce\":\"2021-11-26T12:30:00.000000Z\"}"}]}';
        $this->assertEquals($expected, $signedCommandCollection->toPayloadString());
    }
}
