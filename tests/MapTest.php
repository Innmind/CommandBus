<?php
declare(strict_types = 1);

namespace Tests\Innmind\CommandBus;

use Innmind\CommandBus\{
    Map,
    CommandBus,
};
use Innmind\Immutable\{
    Map as IMap,
    Exception\InvalidArgumentException,
};
use PHPUnit\Framework\TestCase;

class MapTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            CommandBus::class,
            new Map(new IMap('string', 'callable'))
        );
    }

    public function testThrowWhenInvalidHandlerMap()
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('Argument 1 must be of type MapInterface<string, callable>');

        new Map(new IMap('string', 'string'));
    }

    public function testThrowWhenHandlerNotFound()
    {
        $this->expectException(InvalidArgumentException::class);

        (new Map(new IMap('string', 'callable')))(new \stdClass);
    }

    public function testInvokation()
    {
        $count = 0;
        $handle = new Map(
            IMap::of('string', 'callable')
                ('stdClass',function (\stdClass $command) use (&$count) {
                    ++$count;
                    $this->assertSame('foo', $command->bar);
                })
        );

        $command = new \stdClass;
        $command->bar = 'foo';
        $this->assertNull($handle($command));
        $this->assertSame(1, $count);
    }
}
