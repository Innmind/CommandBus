<?php
declare(strict_types = 1);

namespace Innmind\CommandBus;

use Innmind\Immutable\Map as IMap;
use Psr\Log\LoggerInterface;

/**
 * @return array{bus: callable(IMap<string, callable>): CommandBus, enqueue: CommandBus, dequeue: callable(CommandBus): CommandBus, logger: callable(LoggerInterface): (callable(CommandBus): CommandBus)}
 */
function bootstrap(): array
{
    $queue = new Queue;

    /** @psalm-suppress MixedArgumentTypeCoercion */
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
    ];
}
