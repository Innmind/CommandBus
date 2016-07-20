<?php
declare(strict_types = 1);

namespace Innmind\CommandBus;

use Innmind\CommandBus\Exception\InvalidArgumentException;
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
            throw new InvalidArgumentException;
        }

        $this->handlers = $handlers;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($command)
    {
        if (!is_object($command)) {
            throw new InvalidArgumentException;
        }

        $handle = $this->handlers->get(get_class($command));
        $handle($command);
    }
}
