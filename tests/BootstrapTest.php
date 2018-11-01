<?php
declare(strict_types = 1);

namespace Tests\Innmind\CommandBus;

use function Innmind\CommandBus\bootstrap;
use Innmind\CommandBus\{
    Map,
    DequeueCommandBus,
    EnqueueCommandBus,
    LoggerCommandBus
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

        $this->assertInternalType('callable', $bus);
        $this->assertInstanceOf(
            Map::class,
            $bus(new IMap('string', 'callable'))
        );
        $this->assertInstanceOf(EnqueueCommandBus::class, $enqueue);
        $this->assertInternalType('callable', $dequeue);
        $this->assertInstanceOf(
            DequeueCommandBus::class,
            $dequeue($bus(new IMap('string', 'callable')))
        );
        $this->assertInternalType('callable', $log);
        $log = $log($this->createMock(LoggerInterface::class));
        $this->assertInternalType('callable', $log);
        $this->assertInstanceOf(
            LoggerCommandBus::class,
            $log($bus(new IMap('string', 'callable')))
        );
    }

    public function testQueue()
    {
        $buses = bootstrap();
        $bus = $buses['bus'];
        $enqueue = $buses['enqueue'];
        $dequeue = $buses['dequeue'];

        $called = 0;
        $handlers = (new IMap('string', 'callable'))
            ->put('stdClass', function() use ($enqueue): void {
                $enqueue($this);
            })
            ->put(get_class($this), static function() use (&$called): void {
                ++$called;
            });

        $handle = $dequeue($bus($handlers));
        $this->assertNull($handle(new \stdClass));
        $this->assertSame(1, $called);
    }
}
