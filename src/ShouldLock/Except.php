<?php
declare(strict_types = 1);

namespace Innmind\CommandBus\ShouldLock;

use Innmind\CommandBus\ShouldLock;
use Innmind\Immutable\Set;

final class Except implements ShouldLock
{
    private Set $exceptions;

    public function __construct(string ...$exceptions)
    {
        $this->exceptions = Set::strings(...$exceptions);
    }

    public function __invoke(\Throwable $e): bool
    {
        return !$this->exceptions->contains(\get_class($e));
    }
}
