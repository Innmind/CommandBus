<?php
declare(strict_types = 1);

namespace Tests\Innmind\CommandBus;

use Innmind\CommandBus\{
    Map,
    CommandBus,
    Command,
    Handler,
};
use Innmind\Immutable\Map as IMap;
use PHPUnit\Framework\TestCase;

class MapTest extends TestCase
{
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
        $command = $this->createMock(Command::class);
        $handler = $this->createMock(Handler::class);
        $handle = new Map(IMap::of([
            \get_class($command),
            $handler,
        ]));
        $handler
            ->expects($this->once())
            ->method('__invoke')
            ->with($command);

        $this->assertNull($handle($command));
    }
}
