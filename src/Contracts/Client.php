<?php declare(strict_types=1);

namespace Kadena\Contracts;

use Kadena\ValueObjects\Command\SignedCommand;
use Kadena\ValueObjects\Command\SignedCommandCollection;
use Kadena\ValueObjects\RequestKey\RequestKey;
use Kadena\ValueObjects\RequestKey\RequestKeyCollection;
use Symfony\Contracts\HttpClient\ResponseInterface;

interface Client
{
    public function send(SignedCommandCollection $commands): RequestKeyCollection;

    public function local(SignedCommand $command): ResponseInterface;

    public function poll(RequestKeyCollection $requestKeyCollection): ResponseInterface;

    public function listen(RequestKey $requestKey): ResponseInterface;

    public function spv(RequestKey $requestKey, string $targetChainId): string;
}
