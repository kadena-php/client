<?php

namespace Kadena;

use JsonException;
use Kadena\Contracts\Pact as PactContract;
use Kadena\Pact\RequestKey;
use Kadena\Pact\RequestKeyCollection;
use Kadena\Pact\SignedCommand;
use Kadena\Pact\SignedCommandCollection;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class Client implements PactContract
{
    private HttpClientInterface $client;

    public function __construct(private readonly string $apiUrl, HttpClientInterface $httpClient = null)
    {
        if (! $httpClient) {
            $this->client = HttpClient::create();
        } else {
            $this->client = $httpClient;
        }
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function send(SignedCommandCollection $commands): RequestKeyCollection
    {
        $response = $this->client->request('POST', $this->apiUrl . '/api/v1/send', [
            'json' => $commands->toPayload(),
        ]);

        $requestKeys = array_map(static function (string $requestKey) {
            return new RequestKey($requestKey);
        }, $response->toArray()['requestKeys']);

        return new RequestKeyCollection(...$requestKeys);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws JsonException
     */
    public function local(SignedCommand $command): ResponseInterface
    {
        return $this->client->request('POST', $this->apiUrl . '/api/v1/local', [
            'json' => $command->toArray(),
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function poll(RequestKeyCollection $requestKeyCollection): ResponseInterface
    {
        return $this->client->request('POST', $this->apiUrl . '/api/v1/poll', [
            'json' => [
                'requestKeys' => $requestKeyCollection->toPlainArray()
            ]
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function listen(RequestKey $requestKey): ResponseInterface
    {
        return $this->client->request('POST', $this->apiUrl . '/api/v1/listen', [
            'json' => [
                'listen' => $requestKey->key
            ]
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function spv(RequestKey $requestKey, string $targetChainId): string
    {
        $response = $this->client->request('POST', $this->apiUrl . '/api/v1/spv', [
            'json' => [
                'listen' => $requestKey->key
            ]
        ]);

        return $response->getContent();
    }
}
