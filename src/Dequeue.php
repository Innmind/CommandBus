<?php
declare(strict_types = 1);

namespace Innmind\CommandBus;

final class Dequeue implements CommandBus
{
    private CommandBus $handle;
    private Queue $queue;

    public function __construct(CommandBus $handle, Queue $queue)
    {
        $this->handle = $handle;
        $this->queue = $queue;
    }

    public function __invoke(object $command): void
    {
        ($this->handle)($command);

        while ($this->queue->valid()) {
            ($this->handle)($this->queue->dequeue());
        }
    }
}
