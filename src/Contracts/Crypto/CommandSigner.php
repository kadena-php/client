<?php declare(strict_types=1);

namespace Kadena\Contracts\Crypto;

use Kadena\ValueObjects\Command\Command;
use Kadena\ValueObjects\Command\SignedCommand;
use Kadena\ValueObjects\Signer\KeyPairCollection;

interface CommandSigner
{
    public static function sign(Command $command, KeyPairCollection $keyPairs): SignedCommand;
}
