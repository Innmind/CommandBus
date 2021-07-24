<?php
declare(strict_types = 1);

namespace Innmind\CommandBus;

use Innmind\Immutable\{
    Map as IMap,
    Either,
};

final class Map implements CommandBus
{
    /** @var IMap<class-string<Command<Handler>>, Handler> */
    private IMap $handlers;

    /**
     * @param IMap<class-string<Command<Handler>>, Handler> $handlers
     */
    public function __construct(IMap $handlers)
    {
        $this->handlers = $handlers;
    }

    public function __invoke(Command $command): Either
    {
        $class = \get_class($command);

        return $this
            ->handlers
            ->get($class)
            ->map(static fn($handle) => $handle($command))
            ->match(
                static fn($either) => $either,
                static fn() => throw new \LogicException("No handler defined for '$class'"),
            );
    }
}
