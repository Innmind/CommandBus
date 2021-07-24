<?php
declare(strict_types = 1);

namespace Innmind\CommandBus;

use Innmind\Immutable\Map as IMap;

final class Map implements CommandBus
{
    /** @var IMap<class-string<Command>, Handler> */
    private IMap $handlers;

    /**
     * @param IMap<class-string<Command>, Handler> $handlers
     */
    public function __construct(IMap $handlers)
    {
        $this->handlers = $handlers;
    }

    public function __invoke(Command $command): void
    {
        $class = \get_class($command);
        $handle = $this->handlers->get($class)->match(
            static fn($handle) => $handle,
            static fn() => throw new \LogicException("No handler defined for '$class'"),
        );
        $handle($command);
    }
}
