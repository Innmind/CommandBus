<?php
declare(strict_types = 1);

namespace Tests\Innmind\CommandBus;

use Innmind\CommandBus\Queue;
use PHPUnit\Framework\TestCase;

class QueueTest extends TestCase
{
    public function testInterface()
    {
        $queue = new Queue;
        $first = new \stdClass;
        $second = new \stdClass;
        $third = new \stdClass;

        $this->assertFalse($queue->valid());
        $this->assertNull($queue->enqueue($first));
        $this->assertTrue($queue->valid());
        $this->assertNull($queue->enqueue($second));
        $this->assertNull($queue->enqueue($third));
        $this->assertSame($first, $queue->dequeue());
        $this->assertSame($second, $queue->dequeue());
        $this->assertSame($third, $queue->dequeue());
        $this->assertFalse($queue->valid());
    }
}
