<?php
declare(strict_types = 1);

namespace Tests\Innmind\CommandBus;

use Innmind\CommandBus\{
    NullCommandBus,
    CommandBus
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
        $this->assertNull(
            (new NullCommandBus)(new \stdClass)
        );
    }
}
