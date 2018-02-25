<?php
declare(strict_types = 1);

namespace Tests\Innmind\CommandBus;

use Innmind\CommandBus\CommandBus;
use Innmind\Compose\ContainerBuilder\ContainerBuilder;
use Innmind\Url\Path;
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    public function testContainer()
    {
        $called = false;
        $container = (new ContainerBuilder)(
            new Path('container.yml'),
            (new Map('string', 'mixed'))
                ->put(
                    'handlers',
                    (new Map('string', 'callable'))->put(
                        'stdClass',
                        function() use (&$called) {
                            $called = true;
                        }
                    )
                )
        );

        $bus = $container->get('bus');
        $this->assertInstanceOf(CommandBus::class, $bus);
        $this->assertFalse($called);
        $bus->handle(new \stdClass);
        $this->assertTrue($called);
    }
}
