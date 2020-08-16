<?php
declare(strict_types = 1);

namespace Innmind\CommandBus;

use Innmind\DI\ServiceLocator;

final class Container implements CommandBus
{
    private ServiceLocator $get;

    public function __construct(ServiceLocator $get)
    {
        $this->get = $get;
    }

    public function __invoke(object $command): void
    {
        $handle = ($this->get)(\get_class($command));
        /** @psalm-suppress InvalidFunctionCall */
        $handle($command);
    }
}
