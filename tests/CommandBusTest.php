<?php
declare(strict_types = 1);

namespace Tests\Innmind\CommandBus;

use Innmind\CommandBus\{
    CommandBus,
    CommandBusInterface
};
use Innmind\Immutable\Map;

class CommandBusTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            CommandBusInterface::class,
            new CommandBus(new Map('string', 'callable'))
        );
    }

    /**
     * @expectedException Innmind\CommandBus\Exception\InvalidArgumentException
     */
    public function testThrowWhenInvalidHandlerMap()
    {
        new CommandBus(new Map('string', 'string'));
    }

    /**
     * @expectedException Innmind\CommandBus\Exception\InvalidArgumentException
     */
    public function testThrowWhenCommandIsNotAnObject()
    {
        (new CommandBus(new Map('string', 'callable')))->handle([]);
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
