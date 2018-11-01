<?php
declare(strict_types = 1);

namespace Tests\Innmind\CommandBus;

use Innmind\CommandBus\{
    Lock,
    CommandBus,
    ShouldLock,
    Exception\CommandBusLocked,
};
use PHPUnit\Framework\TestCase;

class LockTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            CommandBus::class,
            new Lock($this->createMock(CommandBus::class))
        );
    }

    public function testInvokation()
    {
        $handle = new Lock(
            $inner = $this->createMock(CommandBus::class)
        );
        $command = new \stdClass;
        $inner
            ->expects($this->exactly(2))
            ->method('__invoke')
            ->with($command);

        $this->assertNull($handle($command));
        $this->assertNull($handle($command)); // to assert no lockage
    }

    public function testLock()
    {
        $handle = new Lock(
            $inner = $this->createMock(CommandBus::class)
        );
        $inner
            ->expects($this->once())
            ->method('__invoke')
            ->will($this->throwException($this->createMock(\Throwable::class)));

        try {
            $handle(new \stdClass);
        } catch (\Throwable $e) {
            // expected
        }

        $this->expectException(CommandBusLocked::class);

        $handle(new \stdClass);
    }

    public function testDoesntLockWhenStrategyTellOtherwise()
    {
        $handle = new Lock(
            $inner = $this->createMock(CommandBus::class),
            $strategy = $this->createMock(ShouldLock::class)
        );
        $inner
            ->expects($this->at(0))
            ->method('__invoke')
            ->will($this->throwException($e = $this->createMock(\Throwable::class)));
        $inner
            ->expects($this->at(1))
            ->method('__invoke');
        $strategy
            ->expects($this->once())
            ->method('__invoke')
            ->with($e)
            ->willReturn(false);

        try {
            $handle(new \stdClass);
        } catch (\Throwable $e) {
            // expected
        }

        $this->assertNull($handle(new \stdClass));
    }
}
