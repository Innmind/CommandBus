<?php
declare(strict_types = 1);

namespace Innmind\CommandBus;

use Innmind\CommandBus\Exception\CommandBusLocked;

final class Lock implements CommandBus
{
    private CommandBus $handle;
    private ShouldLock $shouldLock;
    private bool $locked = false;

    public function __construct(CommandBus $handle, ShouldLock $shouldLock = null)
    {
        $this->handle = $handle;
        $this->shouldLock = $shouldLock ?? new ShouldLock\Always;
    }

    public function __invoke(object $command): void
    {
        if ($this->locked) {
            throw new CommandBusLocked;
        }

        try {
            ($this->handle)($command);
        } catch (\Throwable $e) {
            if (($this->shouldLock)($e)) {
                $this->locked = true;
            }
        }
    }
}
