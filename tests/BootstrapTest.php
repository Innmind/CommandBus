<?php
declare(strict_types = 1);

namespace Tests\Innmind\CommandBus;

use function Innmind\CommandBus\bootstrap;
use Innmind\CommandBus\{
    Map,
    Dequeue,
    Enqueue,
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
        $enqueue = $buses['enqueue'];
        $dequeue = $buses['dequeue'];
        $log = $buses['logger'];

        $this->assertIsCallable($bus);
        $this->assertInstanceOf(
            Map::class,
            $bus(IMap::of('string', 'callable'))
        );
        $this->assertInstanceOf(Enqueue::class, $enqueue);
        $this->assertIsCallable($dequeue);
        $this->assertInstanceOf(
            Dequeue::class,
            $dequeue($bus(IMap::of('string', 'callable')))
        );
        $this->assertIsCallable($log);
        $log = $log($this->createMock(LoggerInterface::class));
        $this->assertIsCallable($log);
        $this->assertInstanceOf(
            Logger::class,
            $log($bus(IMap::of('string', 'callable')))
        );
    }

    public function testQueue()
    {
        $buses = bootstrap();
        $bus = $buses['bus'];
        $enqueue = $buses['enqueue'];
        $dequeue = $buses['dequeue'];

        $called = 0;
        $handlers = IMap::of('string', 'callable')
            ('stdClass', function() use ($enqueue): void {
                $enqueue($this);
            })
            (\get_class($this), static function() use (&$called): void {
                ++$called;
            });

        $handle = $dequeue($bus($handlers));
        $this->assertNull($handle(new \stdClass));
        $this->assertSame(1, $called);
    }
}
