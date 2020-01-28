<?php
declare(strict_types = 1);

namespace Innmind\CommandBus;

use Innmind\Immutable\Map as IMap;

final class Map implements CommandBus
{
    /** @var IMap<string, callable> */
    private IMap $handlers;

    /**
     * @param IMap<string, callable> $handlers
     */
    public function __construct(IMap $handlers)
    {
        if (
            (string) $handlers->keyType() !== 'string' ||
            (string) $handlers->valueType() !== 'callable'
        ) {
            throw new \TypeError('Argument 1 must be of type Map<string, callable>');
        }

        $this->handlers = $handlers;
    }

    public function __invoke(object $command): void
    {
        $handle = $this->handlers->get(get_class($command));
        $handle($command);
    }
}
