<?php
declare(strict_types = 1);

namespace Innmind\CommandBus\ShouldLock;

use Innmind\CommandBus\ShouldLock;

final class Always implements ShouldLock
{
    public function __invoke(\Throwable $e): bool
    {
        return true;
    }
}
