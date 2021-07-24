<?php
declare(strict_types = 1);

namespace Innmind\CommandBus;

use Innmind\DI\ServiceLocator;
use Innmind\Immutable\{
    Either,
};

final class Container implements CommandBus
{
    private ServiceLocator $get;

    public function __construct(ServiceLocator $get)
    {
        $this->get = $get;
    }

    public function __invoke(Command $command): Either
    {
        /** @var Handler */
        $handle = ($this->get)(\get_class($command));

        return $handle($command);
    }
}
