<?php declare(strict_types=1);

namespace Kadena\Application\Contracts;

use Kadena\Domain\Command\SignedCommand;
use Kadena\Domain\Command\SignedCommandCollection;
use Kadena\Domain\RequestKey\RequestKey;
use Kadena\Domain\RequestKey\RequestKeyCollection;
use Symfony\Contracts\HttpClient\ResponseInterface;

interface Pact
{
    public function send(SignedCommandCollection $commands): RequestKeyCollection;

    public function local(SignedCommand $command): ResponseInterface;

    public function poll(RequestKeyCollection $requestKeyCollection): ResponseInterface;

    public function listen(RequestKey $requestKey): ResponseInterface;

    public function spv(RequestKey $requestKey, string $targetChainId): string;
}
