<?php
declare(strict_types = 1);

namespace Innmind\CommandBus;

use Innmind\Immutable\Map as IMap;
use Psr\Log\LoggerInterface;

/**
 * @return array{bus: callable(IMap<string, callable>): CommandBus, logger: callable(LoggerInterface): (callable(CommandBus): CommandBus)}
 */
function bootstrap(): array
{
    /** @psalm-suppress MixedArgumentTypeCoercion */
    return [
        'bus' => static function(IMap $handlers): CommandBus {
            return new Map($handlers);
        },
        'logger' => static function(LoggerInterface $logger): callable {
            return static function(CommandBus $bus) use ($logger): CommandBus {
                return new Logger($bus, $logger);
            };
        },
    ];
}
