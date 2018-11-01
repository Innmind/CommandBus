<?php
declare(strict_types = 1);

namespace Tests\Innmind\CommandBus;

use Innmind\CommandBus\{
    CommandBus,
    EnqueueCommandBus,
    Queue,
};
use PHPUnit\Framework\TestCase;

class EnqueueCommandBusTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            CommandBus::class,
            new EnqueueCommandBus(new Queue)
        );
    }

    public function testInvokation()
    {
        $handle = new EnqueueCommandBus($queue = new Queue);
        $command = new \stdClass;

        $this->assertNull($handle($command));
        $this->assertSame($command, $queue->dequeue());
    }
}
