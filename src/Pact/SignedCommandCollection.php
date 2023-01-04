<?php declare(strict_types=1);

namespace Kadena\Pact;

use JsonException;

final class SignedCommandCollection
{
    /**
     * @var SignedCommand[]
     */
    private array $array;

    public function __construct(SignedCommand ...$signedCommand)
    {
        $this->array = $signedCommand;
    }

    /**
     * @return SignedCommand[]
     */
    public function toArray(): array
    {
        return $this->array;
    }

    public function toPayload(): array
    {
        $commands = array_map([$this, 'mapToArray'], $this->array);

        return [
            'cmds' => $commands
        ];
    }

    /**
     * @throws JsonException
     */
    public function toPayloadString(): string
    {
        return json_encode($this->toPayload(), JSON_THROW_ON_ERROR);
    }

    /**
     * @throws JsonException
     */
    private function mapToArray(SignedCommand $signedCommand): array
    {
        return $signedCommand->toArray();
    }
}
