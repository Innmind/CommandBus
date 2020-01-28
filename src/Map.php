<?php
declare(strict_types = 1);

namespace Innmind\CommandBus;

use Innmind\Immutable\Map as IMap;
use function Innmind\Immutable\assertMap;

final class Map implements CommandBus
{
    /** @var IMap<string, callable> */
    private IMap $handlers;

    /**
     * @param IMap<string, callable> $handlers
     */
    public function __construct(IMap $handlers)
    {
        assertMap('string', 'callable', $handlers, 1);

        $this->handlers = $handlers;
    }

    public function __invoke(object $command): void
    {
        $handle = $this->handlers->get(\get_class($command));
        $handle($command);
    }
}
