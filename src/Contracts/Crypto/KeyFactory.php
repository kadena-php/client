<?php declare(strict_types=1);

namespace Kadena\Contracts\Crypto;

use Kadena\ValueObjects\Signer\KeyPair;

interface KeyFactory
{
    public static function generate(): KeyPair;
}
