<?php
declare(strict_types = 1);

namespace Tests\Innmind\CommandBus;

use Innmind\CommandBus\{
    Container,
    CommandBus,
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

        (new Container(new ServiceLocator))(new \stdClass);
    }

    public function testInvokation()
    {
        $count = 0;
        $handle = new Container(
            (new ServiceLocator)->add('stdClass', function() use (&$count) {
                return function (\stdClass $command) use (&$count) {
                    ++$count;
                    $this->assertSame('foo', $command->bar);
                };
            }),
        );

        $command = new \stdClass;
        $command->bar = 'foo';
        $this->assertNull($handle($command));
        $this->assertSame(1, $count);
    }
}
