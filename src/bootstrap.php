<?php
declare(strict_types = 1);

namespace Innmind\CommandBus;

use Innmind\Immutable\Map as IMap;
use Psr\Log\LoggerInterface;

function bootstrap(): array
{
    $queue = new Queue;

    return [
        'bus' => static function(IMap $handlers): CommandBus {
            return new Map($handlers);
        },
        'enqueue' => new Enqueue($queue),
        'dequeue' => static function(CommandBus $bus) use ($queue): CommandBus {
            return new Dequeue($bus, $queue);
        },
        'logger' => static function(LoggerInterface $logger): callable {
            return static function(CommandBus $bus) use ($logger): CommandBus {
                return new Logger($bus, $logger);
            };
        },
        'lock' => static function(ShouldLock $shouldLock = null): callable {
            return static function(CommandBus $bus) use ($shouldLock): CommandBus {
                return new Lock($bus, $shouldLock);
            };
        },
    ];
}
