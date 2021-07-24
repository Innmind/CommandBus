<?php
declare(strict_types = 1);

namespace Tests\Innmind\CommandBus;

use Innmind\CommandBus\{
    Container,
    CommandBus,
    Command,
    Handler,
};
use Innmind\DI\{
    Container as ServiceLocator,
    Exception\ServiceNotFound,
};
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            CommandBus::class,
            new Container(new ServiceLocator)
        );
    }

    public function testThrowWhenHandlerNotFound()
    {
        $this->expectException(ServiceNotFound::class);

        (new Container(new ServiceLocator))($this->createMock(Command::class));
    }

    public function testInvokation()
    {
        $command = $this->createMock(Command::class);
        $handler = $this->createMock(Handler::class);
        $handle = new Container(
            (new ServiceLocator)->add(
                \get_class($command),
                static fn() => $handler,
            ),
        );
        $handler
            ->expects($this->once())
            ->method('__invoke')
            ->with($command);

        $this->assertNull($handle($command));
    }
}
