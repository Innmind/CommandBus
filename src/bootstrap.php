<?php
declare(strict_types = 1);

namespace Innmind\CommandBus;

use Innmind\Immutable\MapInterface;
use Psr\Log\LoggerInterface;

function bootstrap(): array
{
    $queue = new Queue;

    return [
        'bus' => static function(MapInterface $handlers): CommandBus {
            return new Map($handlers);
        },
        'enqueue' => new EnqueueCommandBus($queue),
        'dequeue' => static function(CommandBus $bus) use ($queue): CommandBus {
            return new DequeueCommandBus($bus, $queue);
        },
        'logger' => static function(LoggerInterface $logger): callable {
            return static function(CommandBus $bus) use ($logger): CommandBus {
                return new LoggerCommandBus($bus, $logger);
            };
        },
    ];
}
