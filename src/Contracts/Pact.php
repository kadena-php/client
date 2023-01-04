<?php declare(strict_types=1);

namespace Kadena\Contracts;

use Kadena\Pact\RequestKey;
use Kadena\Pact\RequestKeyCollection;
use Kadena\Pact\SignedCommand;
use Kadena\Pact\SignedCommandCollection;
use Symfony\Contracts\HttpClient\ResponseInterface;

interface Pact
{
    public function send(SignedCommandCollection $commands): RequestKeyCollection;

    public function local(SignedCommand $command): ResponseInterface;

    public function poll(RequestKeyCollection $requestKeyCollection): ResponseInterface;

    public function listen(RequestKey $requestKey): ResponseInterface;

    public function spv(RequestKey $requestKey, string $targetChainId): string;
}
