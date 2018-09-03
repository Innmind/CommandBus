<?php
declare(strict_types = 1);

namespace Innmind\CommandBus;

use Innmind\Immutable\MapInterface;

function bootstrap(): array
{
    $queue = new Queue;

    return [
        'bus' => static function(MapInterface $handlers): CommandBusInterface {
            return new CommandBus($handlers);
        },
        'enqueue' => new EnqueueCommandBus($queue),
        'dequeue' => static function(CommandBusInterface $bus) use ($queue): CommandBusInterface {
            return new DequeueCommandBus($bus, $queue);
        },
    ];
}
