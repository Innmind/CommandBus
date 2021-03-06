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
            private $baz;

            public function __construct()
            {
                $this->baz = new class {
                    private $wat = 'wat';
                    private $str;

                    public function __construct()
                    {
                        $this->str = Str::of('watever');
                    }
                };
            }

            public function bar(): int
            {
                return 42;
            }
        };
        $class = \get_class($command);
        $reference = null;
        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->exactly(2))
            ->method('info')
            ->withConsecutive(
                [
                    'Command about to be executed',
                    $this->callback(static function($data) use (&$reference, $class) {
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
                    }),
                ],
                [
                    'Command executed',
                    $this->callback(static function($data) use (&$reference) {
                        return $data === ['reference' => $reference];
                    }),
                ],
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
        $this->assertTrue(\is_string($reference));
        $this->assertTrue(!empty($reference));
    }
}
