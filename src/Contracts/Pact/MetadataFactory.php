<?php declare(strict_types=1);

namespace Kadena\Contracts\Pact;

use Kadena\ValueObjects\Command\Metadata;

interface MetadataFactory
{
    public function withOptions(array $options): self;
    public function make(): Metadata;
}
