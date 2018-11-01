<?php
declare(strict_types = 1);

namespace Innmind\CommandBus;

interface ShouldLock
{
    public function __invoke(\Throwable $e): bool;
}
