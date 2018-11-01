<?php
declare(strict_types = 1);

namespace Tests\Innmind\CommandBus\ShouldLock;

use Innmind\CommandBus\{
    ShouldLock\Except,
    ShouldLock,
};
use PHPUnit\Framework\TestCase;

class ExceptTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(ShouldLock::class, new Except);
    }

    public function testInvokation()
    {
        $this->assertTrue((new Except)($this->createMock(\Throwable::class)));
        $this->assertFalse((new Except(\TypeError::class))(new \TypeError));
    }
}
