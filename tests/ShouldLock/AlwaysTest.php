<?php
declare(strict_types = 1);

namespace Tests\Innmind\CommandBus\ShouldLock;

use Innmind\CommandBus\{
    ShouldLock\Always,
    ShouldLock,
};
use PHPUnit\Framework\TestCase;

class AlwaysTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(ShouldLock::class, new Always);
    }

    public function testInvokation()
    {
        $this->assertTrue((new Always)($this->createMock(\Throwable::class)));
    }
}
