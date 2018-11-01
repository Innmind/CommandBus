<?php
declare(strict_types = 1);

namespace Innmind\CommandBus;

final class Enqueue implements CommandBus
{
    private $queue;

    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    public function __invoke(object $command): void
    {
        $this->queue->enqueue($command);
    }
}
