<?php
declare(strict_types = 1);

namespace Tests\Innmind\CommandBus;

use Innmind\CommandBus\{
    Enqueue,
    CommandBus,
    Queue,
};
use PHPUnit\Framework\TestCase;

class EnqueueTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            CommandBus::class,
            new Enqueue(new Queue)
        );
    }

    public function testInvokation()
    {
        $handle = new Enqueue($queue = new Queue);
        $command = new \stdClass;

        $this->assertNull($handle($command));
        $this->assertSame($command, $queue->dequeue());
    }
}
