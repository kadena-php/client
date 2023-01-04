<?php declare(strict_types=1);

namespace Kadena\Pact;

enum PayloadType: string
{
    case EXECUTE = 'exec';
    case CONTINUE = 'cont';
}
