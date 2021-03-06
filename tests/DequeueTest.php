<?php
declare(strict_types = 1);

namespace Tests\Innmind\CommandBus;

use Innmind\CommandBus\{
    Dequeue,
    CommandBus,
    Queue,
};
use PHPUnit\Framework\TestCase;

class DequeueTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            CommandBus::class,
            new Dequeue(
                $this->createMock(CommandBus::class),
                new Queue
            )
        );
    }

    public function testInvokeWithNoEnqueue()
    {
        $handle = new Dequeue(
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
        $handle = new Dequeue(
            $inner = $this->createMock(CommandBus::class),
            $queue = new Queue
        );
        $command = new \stdClass;
        $inner
            ->expects($this->exactly(2))
            ->method('__invoke')
            ->withConsecutive(
                [$this->callback(static function($command) use ($queue): bool {
                    $queue->enqueue($command);

                    return true;
                })],
                [$command],
            );

        $this->assertNull($handle($command));
    }
}
