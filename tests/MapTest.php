<?php
declare(strict_types = 1);

namespace Tests\Innmind\CommandBus;

use Innmind\CommandBus\{
    Map,
    CommandBus,
};
use Innmind\Immutable\Map as IMap;
use PHPUnit\Framework\TestCase;

class MapTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            CommandBus::class,
            new Map(IMap::of())
        );
    }

    public function testThrowWhenHandlerNotFound()
    {
        $this->expectException(\LogicException::class);

        (new Map(IMap::of()))(new \stdClass);
    }

    public function testInvokation()
    {
        $count = 0;
        $handle = new Map(IMap::of([
            'stdClass',
            function(\stdClass $command) use (&$count) {
                ++$count;
                $this->assertSame('foo', $command->bar);
            },
        ]));

        $command = new \stdClass;
        $command->bar = 'foo';
        $this->assertNull($handle($command));
        $this->assertSame(1, $count);
    }
}
