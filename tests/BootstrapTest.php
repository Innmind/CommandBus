<?php
declare(strict_types = 1);

namespace Tests\Innmind\CommandBus;

use function Innmind\CommandBus\bootstrap;
use Innmind\CommandBus\{
    Map,
    Logger,
};
use Innmind\Immutable\Map as IMap;
use Psr\Log\LoggerInterface;
use PHPUnit\Framework\TestCase;

class BootstrapTest extends TestCase
{
    public function testBootstrap()
    {
        $buses = bootstrap();
        $bus = $buses['bus'];
        $log = $buses['logger'];

        $this->assertIsCallable($bus);
        $this->assertInstanceOf(
            Map::class,
            $bus(IMap::of('string', 'callable'))
        );
        $this->assertIsCallable($log);
        $log = $log($this->createMock(LoggerInterface::class));
        $this->assertIsCallable($log);
        $this->assertInstanceOf(
            Logger::class,
            $log($bus(IMap::of('string', 'callable')))
        );
    }
}
