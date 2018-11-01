<?php
declare(strict_types = 1);

namespace Innmind\CommandBus;

final class NullCommandBus implements CommandBusInterface
{
    public function __invoke(object $command): void
    {
    }
}
