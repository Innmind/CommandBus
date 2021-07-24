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
use Innmind\Immutable\{
    Either,
    SideEffect,
};
use PHPUnit\Framework\TestCase;
use Innmind\BlackBox\{
    PHPUnit\BlackBox,
    Set,
};

class ContainerTest extends TestCase
{
    use BlackBox;

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
        $this
            ->forAll(new Set\Either(
                Set\Elements::of(Either::right(new SideEffect)),
                Set\Decorate::immutable(
                    static fn($error) => Either::left($error),
                    Set\AnyType::any(),
                ),
            ))
            ->then(function($expected) {
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
                    ->with($command)
                    ->willReturn($expected);

                $this->assertSame($expected, $handle($command));
            });
    }
}
