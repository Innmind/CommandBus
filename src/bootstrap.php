<?php
declare(strict_types = 1);

namespace Innmind\CommandBus;

use Innmind\Immutable\MapInterface;
use Psr\Log\LoggerInterface;

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
        'logger' => static function(LoggerInterface $logger): callable {
            return static function(CommandBusInterface $bus) use ($logger): CommandBusInterface {
                return new LoggerCommandBus($bus, $logger);
            };
        },
    ];
}
