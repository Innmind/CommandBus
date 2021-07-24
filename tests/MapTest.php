<?php
declare(strict_types = 1);

namespace Tests\Innmind\CommandBus;

use Innmind\CommandBus\{
    Map,
    CommandBus,
    Command,
    Handler,
};
use Innmind\Immutable\{
    Map as IMap,
    Either,
    SideEffect,
};
use PHPUnit\Framework\TestCase;
use Innmind\BlackBox\{
    PHPUnit\BlackBox,
    Set,
};

class MapTest extends TestCase
{
    use BlackBox;

    public function testInterface()
    {
        $this->assertInstanceOf(
            CommandBus::class,
            new Map(IMap::of())
        );
    }

    public function testThrowWhenHandlerNotFound()
    {
        $this->expectException(\LogicException::class);

        (new Map(IMap::of()))($this->createMock(Command::class));
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
                $handle = new Map(IMap::of([
                    \get_class($command),
                    $handler,
                ]));
                $handler
                    ->expects($this->once())
                    ->method('__invoke')
                    ->with($command)
                    ->willReturn($expected);

                $this->assertSame($expected, $handle($command));
            });
    }
}
