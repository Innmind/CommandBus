<?php
declare(strict_types = 1);

namespace Tests\Innmind\CommandBus;

use Innmind\CommandBus\{
    CommandBus,
    CommandBusInterface
};
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class CommandBusTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            CommandBusInterface::class,
            new CommandBus(new Map('string', 'callable'))
        );
    }

    public function testThrowWhenInvalidHandlerMap()
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('Argument 1 must be of type MapInterface<string, callable>');

        new CommandBus(new Map('string', 'string'));
    }

    /**
     * @expectedException Innmind\Immutable\Exception\InvalidArgumentException
     */
    public function testThrowWhenHandlerNotFound()
    {
        (new CommandBus(new Map('string', 'callable')))->handle(new \stdClass);
    }

    public function testHandle()
    {
        $count = 0;
        $bus = new CommandBus(
            (new Map('string', 'callable'))->put(
                'stdClass',
                function (\stdClass $command) use (&$count) {
                    ++$count;
                    $this->assertSame('foo', $command->bar);
                }
            )
        );

        $command = new \stdClass;
        $command->bar = 'foo';
        $this->assertNull($bus->handle($command));
        $this->assertSame(1, $count);
    }
}
