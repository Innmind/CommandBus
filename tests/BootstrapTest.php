<?php
declare(strict_types = 1);

namespace Tests\Innmind\CommandBus;

use function Innmind\CommandBus\bootstrap;
use Innmind\CommandBus\{
    CommandBus,
    DequeueCommandBus,
    EnqueueCommandBus,
    LoggerCommandBus
};
use Innmind\Immutable\Map;
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
            CommandBus::class,
            $bus(new Map('string', 'callable'))
        );
        $this->assertInstanceOf(EnqueueCommandBus::class, $enqueue);
        $this->assertInternalType('callable', $dequeue);
        $this->assertInstanceOf(
            DequeueCommandBus::class,
            $dequeue($bus(new Map('string', 'callable')))
        );
        $this->assertInternalType('callable', $log);
        $log = $log($this->createMock(LoggerInterface::class));
        $this->assertInternalType('callable', $log);
        $this->assertInstanceOf(
            LoggerCommandBus::class,
            $log($bus(new Map('string', 'callable')))
        );
    }

    public function testQueue()
    {
        $buses = bootstrap();
        $bus = $buses['bus'];
        $enqueue = $buses['enqueue'];
        $dequeue = $buses['dequeue'];

        $called = 0;
        $handlers = (new Map('string', 'callable'))
            ->put('stdClass', function() use ($enqueue): void {
                $enqueue->handle($this);
            })
            ->put(get_class($this), static function() use (&$called): void {
                ++$called;
            });

        $bus = $dequeue($bus($handlers));
        $this->assertNull($bus->handle(new \stdClass));
        $this->assertSame(1, $called);
    }
}
