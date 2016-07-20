<?php
declare(strict_types = 1);

namespace Innmind\CommandBus;

interface CommandBusInterface
{
    /**
     * @param object $command
     *
     * @throws InvalidArgumentException If the command is not an object
     *
     * @return void
     */
    public function handle($command);
}
