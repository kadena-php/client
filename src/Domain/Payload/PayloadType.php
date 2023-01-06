<?php declare(strict_types=1);

namespace Kadena\Domain\Payload;

enum PayloadType: string
{
    case EXECUTE = 'exec';
    case CONTINUE = 'cont';
}
