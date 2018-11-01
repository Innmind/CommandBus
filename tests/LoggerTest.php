<?php
declare(strict_types = 1);

namespace Tests\Innmind\CommandBus;

use Innmind\CommandBus\{
    Logger,
    CommandBus,
};
use Innmind\Immutable\Str;
use Psr\Log\LoggerInterface;
use PHPUnit\Framework\TestCase;

class LoggerTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            CommandBus::class,
            new Logger(
                $this->createMock(CommandBus::class),
                $this->createMock(LoggerInterface::class)
            )
        );
    }

    public function testInvokation()
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
        $baz->str = new Str('watever');
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
                            'bar' => null,
                            'baz' => [
                                'wat' => 'wat',
                                'str' => [
                                    'value' => 'watever',
                                    'encoding' => 'UTF-8',
                                ],
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
        $innerBus = $this->createMock(CommandBus::class);
        $innerBus
            ->expects($this->once())
            ->method('__invoke')
            ->with($command);
        $handle = new Logger(
            $innerBus,
            $logger
        );

        $this->assertNull($handle($command));
        $this->assertTrue(is_string($reference));
        $this->assertTrue(!empty($reference));
    }
}
