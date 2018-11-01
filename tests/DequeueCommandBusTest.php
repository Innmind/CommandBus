<?php
declare(strict_types = 1);

namespace Tests\Innmind\CommandBus;

use Innmind\CommandBus\{
    CommandBus,
    DequeueCommandBus,
    Queue,
};
use PHPUnit\Framework\TestCase;

class DequeueCommandBusTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            CommandBus::class,
            new DequeueCommandBus(
                $this->createMock(CommandBus::class),
                new Queue
            )
        );
    }

    public function testInvokeWithNoEnqueue()
    {
        $handle = new DequeueCommandBus(
            $inner = $this->createMock(CommandBus::class),
            new Queue
        );
        $command = new \stdClass;
        $inner
            ->expects($this->once())
            ->method('__invoke')
            ->with($command);

        $this->assertNull($handle($command));
    }

    public function testInvokeWithEnqueue()
    {
        $handle = new DequeueCommandBus(
            $inner = $this->createMock(CommandBus::class),
            $queue = new Queue
        );
        $command = new \stdClass;
        $inner
            ->expects($this->at(0))
            ->method('__invoke')
            ->with($this->callback(static function($command) use ($queue): bool {
                $queue->enqueue($command);

                return true;
            }));
        $inner
            ->expects($this->at(1))
            ->method('__invoke')
            ->with($command);

        $this->assertNull($handle($command));
    }
}
