<?php
declare(strict_types = 1);

namespace Innmind\CommandBus;

interface CommandBus
{
    public function __invoke(Command $command): void;
}
