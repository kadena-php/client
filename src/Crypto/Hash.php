<?php declare(strict_types=1);

namespace Kadena\Crypto;

use Kadena\Crypto\Contracts\Hash as HashContract;
use SodiumException;

final class Hash implements HashContract
{
    /**
     * @throws SodiumException
     */
    public static function generic(string $string): string
    {
        return sodium_crypto_generichash($string);
    }
}
