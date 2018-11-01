<?php
declare(strict_types = 1);

namespace Innmind\CommandBus;

interface CommandBusInterface
{
    public function handle(object $command): void;
}
