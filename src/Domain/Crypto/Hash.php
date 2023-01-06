<?php declare(strict_types=1);

namespace Kadena\Domain\Crypto;

use Kadena\Application\Contracts\Hash as HashContract;
use SodiumException;
use function Kadena\Crypto\sodium_crypto_generichash;

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
