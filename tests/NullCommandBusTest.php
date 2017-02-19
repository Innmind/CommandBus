<?php
declare(strict_types = 1);

namespace Tests\Innmind\CommandBus;

use Innmind\CommandBus\{
    NullCommandBus,
    CommandBusInterface
};
use PHPUnit\Framework\TestCase;

class NullCommandBusTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            CommandBusInterface::class,
            new NullCommandBus
        );
    }

    /**
     * @expectedException Innmind\CommandBus\Exception\InvalidArgumentException
     */
    public function testThrowWhenCommandIsNotAnObject()
    {
        (new NullCommandBus)->handle([]);
    }

    public function testHandle()
    {
        $this->assertNull(
            (new NullCommandBus)->handle(new \stdClass)
        );
    }
}
