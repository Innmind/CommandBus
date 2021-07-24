<?php
declare(strict_types = 1);

namespace Tests\Innmind\CommandBus;

use Innmind\CommandBus\{
    NullCommandBus,
    CommandBus,
    Command,
};
use Innmind\Immutable\{
    Either,
    SideEffect,
};
use PHPUnit\Framework\TestCase;

class NullCommandBusTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            CommandBus::class,
            new NullCommandBus
        );
    }

    public function testInvokation()
    {
        $this->assertEquals(
            Either::right(new SideEffect),
            (new NullCommandBus)($this->createMock(Command::class)),
        );
    }
}
