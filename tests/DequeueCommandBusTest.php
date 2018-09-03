<?php
declare(strict_types = 1);

namespace Tests\Innmind\CommandBus;

use Innmind\CommandBus\{
    CommandBusInterface,
    DequeueCommandBus,
    Queue,
};
use PHPUnit\Framework\TestCase;

class DequeueCommandBusTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            CommandBusInterface::class,
            new DequeueCommandBus(
                $this->createMock(CommandBusInterface::class),
                new Queue
            )
        );
    }

    public function testHandleWithNoEnqueue()
    {
        $bus = new DequeueCommandBus(
            $inner = $this->createMock(CommandBusInterface::class),
            new Queue
        );
        $command = new \stdClass;
        $inner
            ->expects($this->once())
            ->method('handle')
            ->with($command);

        $this->assertNull($bus->handle($command));
    }

    public function testHandleWithEnqueue()
    {
        $bus = new DequeueCommandBus(
            $inner = $this->createMock(CommandBusInterface::class),
            $queue = new Queue
        );
        $command = new \stdClass;
        $inner
            ->expects($this->at(0))
            ->method('handle')
            ->with($this->callback(static function($command) use ($queue): bool {
                $queue->enqueue($command);

                return true;
            }));
        $inner
            ->expects($this->at(1))
            ->method('handle')
            ->with($command);

        $this->assertNull($bus->handle($command));
    }
}
