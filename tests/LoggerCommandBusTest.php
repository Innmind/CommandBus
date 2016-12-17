<?php
declare(strict_types = 1);

namespace Tests\Innmind\CommandBus;

use Innmind\CommandBus\{
    LoggerCommandBus,
    CommandBusInterface
};
use Psr\Log\LoggerInterface;

class LoggerCommandBusTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            CommandBusInterface::class,
            new LoggerCommandBus(
                $this->createMock(CommandBusInterface::class),
                $this->createMock(LoggerInterface::class)
            )
        );
    }

    public function testHandle()
    {
        $command = new class() {
            private $foo = 'bar';
            private $bar;
            public $baz;

            public function bar(): int
            {
                return 42;
            }
        };
        $command->baz = $baz = new \stdClass;
        $baz->wat = 'wat';
        $class = get_class($command);
        $reference = null;
        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->at(0))
            ->method('info')
            ->with(
                'Command about to be executed',
                $this->callback(function($data) use (&$reference, $class) {
                    $reference = $data['reference'] ?? null;

                    return $data['class'] === $class &&
                        $data['data'] === [
                            'foo' => 'bar',
                            'bar' => 42,
                            'baz' => [
                                'wat' => 'wat',
                            ],
                        ];
                })
            );
        $logger
            ->expects($this->at(1))
            ->method('info')
            ->with(
                'Command executed',
                $this->callback(function($data) use (&$reference) {
                    return $data === ['reference' => $reference];
                })
            );
        $innerBus = $this->createMock(CommandBusInterface::class);
        $innerBus
            ->expects($this->once())
            ->method('handle')
            ->with($command);
        $bus = new LoggerCommandBus(
            $innerBus,
            $logger
        );

        $this->assertNull($bus->handle($command));
        $this->assertTrue(is_string($reference));
        $this->assertTrue(!empty($reference));
    }

    /**
     * @expectedException Innmind\CommandBus\Exception\InvalidArgumentException
     */
    public function testThrowWhenNotHandlingObject()
    {
        $bus = new LoggerCommandBus(
            $this->createMock(CommandBusInterface::class),
            $this->createMock(LoggerInterface::class)
        );

        $bus->handle('foo');
    }
}
