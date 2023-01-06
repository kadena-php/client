<?php declare(strict_types=1);

namespace HergenD\PactPhp\Tests\Unit;

use Carbon\Carbon;
use Kadena\Application\Client;
use Kadena\Application\Command;
use Kadena\Domain\Command\SignedCommand;
use Kadena\Domain\Command\SignedCommandCollection;
use Kadena\Domain\Meta\Meta;
use Kadena\Domain\Payload\ExecutePayload;
use Kadena\Domain\Payload\Payload;
use Kadena\Domain\Payload\PayloadType;
use Kadena\Domain\RequestKey\RequestKey;
use Kadena\Domain\RequestKey\RequestKeyCollection;
use Kadena\Domain\Signature\Signature;
use Kadena\Domain\Signature\SignatureCollection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class ClientTest extends TestCase
{
    private SignedCommand $signedCommand;

    public function setUp(): void
    {
        parent::setUp();

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

        $this->signedCommand = new SignedCommand('hash', $signatures, $command);
    }

    /** @test */
    public function it_should_send_a_signed_command_to_the_local_endpoint_and_return_a_response_object(): void
    {
        $expectedRequestData = $this->signedCommand->toArray();

        $expectedResponseData = [
            'gas' => 123,
            'result' => [
                'status' => 'success',
                'data' => 3
            ],
            'reqKey' => 'y3aWL72-3wAy7vL9wcegGXnstH0lHi-q-cfxkhD5JCw',
            'logs' => 'wsATyGqckuIvlm89hhd2j4t6RMkCrcwJe_oeCYr7Th8',
            'metaData' => null,
            'continuation' => null,
            'txId' => '456',
            'events' => [
                [
                    'name' => 'TRANSFER',
                    'params' => [
                        'Alice',
                        'Bob',
                        10
                    ],
                    'module' => 'coin',
                    'moduleHash' => 'ut_J_ZNkoyaPUEJhiwVeWnkSQn9JT9sQCWKdjjVVrWo'
                ]
            ]
        ];
        ;

        $mockResponseJson = json_encode($expectedResponseData, JSON_THROW_ON_ERROR);

        $mockResponse = new MockResponse($mockResponseJson, [
            'http_code' => 200,
            'response_headers' => ['Content-Type: application/json'],
        ]);

        $httpClient = new MockHttpClient($mockResponse);

        $subject = new Client('http://localhost:8000', $httpClient);

        $responseData = $subject->local($this->signedCommand);

        $this->assertSame('POST', $mockResponse->getRequestMethod());
        $this->assertSame('http://localhost:8000/api/v1/local', $mockResponse->getRequestUrl());
        $this->assertContains(
            'Content-Type: application/json',
            $mockResponse->getRequestOptions()['headers']
        );

        $this->assertSame($expectedRequestData, json_decode($mockResponse->getRequestOptions()['body'], true));

        $this->assertSame($responseData->toArray(), $expectedResponseData);
    }

    /** @test */
    public function it_should_send_a_collection_of_signed_commands_to_the_send_endpoint_and_return_a_collection_of_request_keys(): void
    {
        $expectedRequestData = (new SignedCommandCollection($this->signedCommand))->toPayload();

        $requestKeyString = 'y3aWL72-3wAy7vL9wcegGXnstH0lHi-q-cfxkhD5JCw';

        $mockResponseJson = json_encode([
            'requestKeys' => [
                $requestKeyString
            ]
        ], JSON_THROW_ON_ERROR);

        $mockResponse = new MockResponse($mockResponseJson, [
            'http_code' => 200,
            'response_headers' => ['Content-Type: application/json'],
        ]);

        $httpClient = new MockHttpClient($mockResponse);

        $subject = new Client('http://localhost:8000', $httpClient);

        $responseData = $subject->send(new SignedCommandCollection($this->signedCommand));

        $expected = new RequestKey($requestKeyString);

        $this->assertSame('POST', $mockResponse->getRequestMethod());
        $this->assertSame('http://localhost:8000/api/v1/send', $mockResponse->getRequestUrl());
        $this->assertContains(
            'Content-Type: application/json',
            $mockResponse->getRequestOptions()['headers']
        );

        $this->assertSame($expectedRequestData, json_decode($mockResponse->getRequestOptions()['body'], true));

        $this->assertEquals($responseData->first(), $expected);
    }

    /** @test */
    public function it_should_send_a_request_key_to_the_listen_endpoint_and_return_a_response_object(): void
    {
        $requestKeyString = 'y3aWL72-3wAy7vL9wcegGXnstH0lHi-q-cfxkhD5JCw';
        $requestKey = new RequestKey($requestKeyString);

        $expectedRequestData = [
            'listen' => $requestKeyString
        ];

        $expectedResponseData = [
            'gas' => 123,
            'result' => [
                'status' => 'success',
                'data' => 3
            ],
            'reqKey' => 'y3aWL72-3wAy7vL9wcegGXnstH0lHi-q-cfxkhD5JCw',
            'logs' => 'wsATyGqckuIvlm89hhd2j4t6RMkCrcwJe_oeCYr7Th8',
            'metaData' => null,
            'continuation' => null,
            'txId' => '456',
            'events' => [
                [
                    'name' => 'TRANSFER',
                    'params' => [
                        'Alice',
                        'Bob',
                        10
                    ],
                    'module' => 'coin',
                    'moduleHash' => 'ut_J_ZNkoyaPUEJhiwVeWnkSQn9JT9sQCWKdjjVVrWo'
                ]
            ]
        ];

        $mockResponseJson = json_encode($expectedResponseData, JSON_THROW_ON_ERROR);

        $mockResponse = new MockResponse($mockResponseJson, [
            'http_code' => 200,
            'response_headers' => ['Content-Type: application/json'],
        ]);

        $httpClient = new MockHttpClient($mockResponse);

        $subject = new Client('http://localhost:8000', $httpClient);

        $responseData = $subject->listen($requestKey);

        $this->assertSame('POST', $mockResponse->getRequestMethod());
        $this->assertSame('http://localhost:8000/api/v1/listen', $mockResponse->getRequestUrl());
        $this->assertContains(
            'Content-Type: application/json',
            $mockResponse->getRequestOptions()['headers']
        );

        $this->assertSame($expectedRequestData, json_decode($mockResponse->getRequestOptions()['body'], true));

        $this->assertSame($responseData->toArray(), $expectedResponseData);
    }

    /** @test */
    public function it_should_send_a_collection_of_request_keys_to_the_poll_endpoint_and_return_a_response_object(): void
    {
        $requestKeyString = 'y3aWL72-3wAy7vL9wcegGXnstH0lHi-q-cfxkhD5JCw';
        $requestKey = new RequestKey($requestKeyString);

        $expectedRequestData = [
            'requestKeys' => [$requestKeyString]
        ];

        // Similar response as the listen function but a response per request key
        $expectedResponseData = [
            [
                'gas' => 123,
                'result' => [
                    'status' => 'success',
                    'data' => 3
                ],
                'reqKey' => 'y3aWL72-3wAy7vL9wcegGXnstH0lHi-q-cfxkhD5JCw',
                'logs' => 'wsATyGqckuIvlm89hhd2j4t6RMkCrcwJe_oeCYr7Th8',
                'metaData' => null,
                'continuation' => null,
                'txId' => '456',
                'events' => [
                    [
                        'name' => 'TRANSFER',
                        'params' => [
                            'Alice',
                            'Bob',
                            10
                        ],
                        'module' => 'coin',
                        'moduleHash' => 'ut_J_ZNkoyaPUEJhiwVeWnkSQn9JT9sQCWKdjjVVrWo'
                    ]
                ]
            ]
        ];

        $mockResponseJson = json_encode($expectedResponseData, JSON_THROW_ON_ERROR);

        $mockResponse = new MockResponse($mockResponseJson, [
            'http_code' => 200,
            'response_headers' => ['Content-Type: application/json'],
        ]);

        $httpClient = new MockHttpClient($mockResponse);

        $subject = new Client('http://localhost:8000', $httpClient);

        $responseData = $subject->poll(new RequestKeyCollection($requestKey));

        $this->assertSame('POST', $mockResponse->getRequestMethod());
        $this->assertSame('http://localhost:8000/api/v1/poll', $mockResponse->getRequestUrl());
        $this->assertContains(
            'Content-Type: application/json',
            $mockResponse->getRequestOptions()['headers']
        );

        $this->assertSame($expectedRequestData, json_decode($mockResponse->getRequestOptions()['body'], true));

        $this->assertSame($responseData->toArray(), $expectedResponseData);
    }

    /** @test */
    public function it_should_send_a_request_key_and_target_chain_id_to_the_spv_endpoint_and_return_a_proof_string(): void
    {
        $requestKeyString = 'y3aWL72-3wAy7vL9wcegGXnstH0lHi-q-cfxkhD5JCw';
        $requestKey = new RequestKey($requestKeyString);

        $expectedRequestData = [
            'listen' => $requestKeyString
        ];

        $expectedResponseData = 'eyJzdWJqZWN0Ijp7ImlucHV0IjoiQUJSN0ltZGhjeUk2TlRRMExDSnlaWE4xYkhRaU9uc2ljM1JoZEhWeklqb2ljM1ZqWTJWemN5SXNJbVJoZEdFaU9pSlhjbWwwWlNCemRXTmpaV1ZrWldRaWZTd2ljbVZ4UzJWNUlqb2lZa0Y0TjNOd1dqZFdUbUpZWTNocVZFUkNTamt5U21SdlUyVlFjWGx0U25KNWNXOUNhMWcyUkVoYWJ5SXNJbXh2WjNNaU9pSnBRVTF4Y0ZwaVUxSkRaR2hQUzA1YVVYZzFTMHBOTFZOUlNGRlZXRzF4UlZoUlRIRkNUVVpSVFVkSklpd2laWFpsYm5SeklqcGJleUp3WVhKaGJYTWlPbHNpZEdWemRDMXpaVzVrWlhJaUxDSXpaRGxsT1dZeFptSTBZemt6TnpneU5qWmpZV1JrTmpObE4yRTBOMkkzWVRZME5UTmlaVGsyTVdSaU1ETTNNMlkxWXpWbVlUUXdZV05sWlRaaVpHVm1JaXd4WFN3aWJtRnRaU0k2SWxSU1FVNVRSa1ZTSWl3aWJXOWtkV3hsSWpwN0ltNWhiV1Z6Y0dGalpTSTZiblZzYkN3aWJtRnRaU0k2SW1OdmFXNGlmU3dpYlc5a2RXeGxTR0Z6YUNJNkluVjBYMHBmV2s1cmIzbGhVRlZGU21ocGQxWmxWMjVyVTFGdU9VcFVPWE5SUTFkTFpHcHFWbFp5VjI4aWZWMHNJbTFsZEdGRVlYUmhJanB1ZFd4c0xDSmpiMjUwYVc1MVlYUnBiMjRpT201MWJHd3NJblI0U1dRaU9qRXhOams1TkRaOSJ9LCJhbGdvcml0aG0iOiJTSEE1MTJ0XzI1NiIsIm9iamVjdCI6IkFBQUFFQUFBQUFBQUFBQUJBUGhpTkRUdEFHT0l4dWE4OTFYUGU0NVFRS2QtTFdOekNpc0JDeHlmeDliQ0FPUkRnUUR2RFRrWmdOTzZ2M1ZpbU1wZ2ZGd2kyQm1mZ29jRVdwVmxRRW9EQWVoT1JPeFdBckJidXpldnZLTUdQZTB1RlVfUE8yejM3VC0tY0thdDZ1d3pBVm9DbFVrU1lXaXRDODF0TERVd2JYYVFWRTdnZFp1ckN6d0RiZUlBdlpBcUFKVThWZHZkMS1nYmo2UEtIVXdWQm00UWRvNl9YUkpYdHdKTGE4a0N3OWJhQWQtbXRubnlsUkczOC1WcTZzZmlZWm0xd2tKejhZcU5ZT2gwbVZCTktFR1VBTkdQWlB4NGFhMWFDdTJ1Ty1VRkJXLWxLbFdFeFU0a2JjMkszOFZCT21ZeEFDakxpdjMwazdBaGdwVXBCWUIxcEYwWFRqTmU4d3k4aHQta2FveFFKbTZpQVlXSkFYZlpXZERNdkQ3Z1UydUItWFdTVUh3bVpvM3NzV0stRzh1OTIxempBTzllbVBkOFJRVk5jOWZWZWJHN0lMb2lqVDlYMm9Db1p2Q00xQ29yR3laUUFTLVVZd3c4dkJ1bEVVYXlxaHZEQUFreUthbHk1TXk1bzJYVXZpZlZsNkg5QUM5ZXZsczVxMXh2bGhQbE9UWnJZNVB2SDNFbDd3dTBZTTJQYmZzaE1lUGFBUFpZRFJoWncyXzBVM1hIZllQbmJ6QlQ4bkc3a2gtR09kRTBTcFFCNEVOQ0FVWGEzcGVoMnhVd2dCVHd5WFVvc3RDRjNqQ21Scm9ZRGlEUTVGTGhYNkVQQUdlMUF2cFhJazZFM2tpdnUxY1N4aVFYV0hUcW1pdEUwLTVYaVpjNU4zQ3ZBS1dMNmM1RDdQSV84aW0zbG04cWhtZl84UXp3d2ZFcVpXQXZoQ0dWc1VVdCIsImNoYWluIjoxfQ';

        $mockResponseJson = $expectedResponseData;

        $mockResponse = new MockResponse($mockResponseJson, [
            'http_code' => 200,
            'response_headers' => ['Content-Type: application/json'],
        ]);

        $httpClient = new MockHttpClient($mockResponse);

        $subject = new Client('http://localhost:8000', $httpClient);

        $responseData = $subject->spv($requestKey, '2');

        $this->assertSame('POST', $mockResponse->getRequestMethod());
        $this->assertSame('http://localhost:8000/api/v1/spv', $mockResponse->getRequestUrl());
        $this->assertContains(
            'Content-Type: application/json',
            $mockResponse->getRequestOptions()['headers']
        );

        $this->assertSame($expectedRequestData, json_decode($mockResponse->getRequestOptions()['body'], true));

        $this->assertSame($responseData, $expectedResponseData);
    }
}
