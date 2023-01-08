<?php declare(strict_types=1);

namespace Kadena\ValueObjects\Command\Payload;

enum PayloadType: string
{
    case EXECUTE = 'exec';
    case CONTINUE = 'cont';
}
