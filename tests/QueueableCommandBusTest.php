<?php
declare(strict_types = 1);

namespace Tests\Innmind\CommandBus;

use Innmind\CommandBus\{
    QueueableCommandBus,
    CommandBus,
    CommandBusInterface
};
use Innmind\Immutable\Map;

class QueueableCommandBusTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            CommandBusInterface::class,
            new QueueableCommandBus($this->createMock(CommandBusInterface::class))
        );
    }

    /**
     * @expectedException Innmind\CommandBus\Exception\InvalidArgumentException
     */
    public function testThrowWhenCommandIsNotAnObject()
    {
        (new QueueableCommandBus(
            $this->createMock(CommandBusInterface::class)
        ))
            ->handle([]);
    }

    public function testHandle()
    {
        $count = 0;
        $command = new class{};
        $commandClass = get_class($command);
        $bus = new CommandBus(
            (new Map('string', 'callable'))
                ->put(
                    $commandClass,
                    function($command) use (&$count) {
                        ++$count;
                        $this->commandBus->handle(new \stdClass);
                        $this->assertSame(1, $count);
                    }
                )
                ->put(
                    'stdClass',
                    function(\stdClass $command) use (&$count) {
                        ++$count;
                        $this->assertSame(2, $count);
                    }
                )
        );
        $this->commandBus = new QueueableCommandBus($bus);

        $this->assertNull($this->commandBus->handle($command));
        $this->assertSame(2, $count);
        unset($this->commandBus);
    }

    public function testResetWhenExceptionThrown()
    {
        $bus = $this->createMock(CommandBusInterface::class);
        $bus
            ->expects($this->at(0))
            ->method('handle')
            ->will($this->throwException(new \Exception));
        $bus
            ->expects($this->at(1))
            ->method('handle');
        $queue = new QueueableCommandBus($bus);

        try {
            $this->assertNull($queue->handle(new \stdClass));
        } catch (\Exception $e) {
            //pass
        }
        $this->assertNull($queue->handle(new \stdClass));
    }
}
