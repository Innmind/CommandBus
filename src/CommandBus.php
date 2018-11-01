<?php
declare(strict_types = 1);

namespace Innmind\CommandBus;

use Innmind\Immutable\MapInterface;

final class CommandBus implements CommandBusInterface
{
    private $handlers;

    public function __construct(MapInterface $handlers)
    {
        if (
            (string) $handlers->keyType() !== 'string' ||
            (string) $handlers->valueType() !== 'callable'
        ) {
            throw new \TypeError('Argument 1 must be of type MapInterface<string, callable>');
        }

        $this->handlers = $handlers;
    }

    public function handle(object $command): void
    {
        $handle = $this->handlers->get(get_class($command));
        $handle($command);
    }
}
