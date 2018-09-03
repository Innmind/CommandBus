<?php
declare(strict_types = 1);

namespace Innmind\CommandBus;

final class EnqueueCommandBus implements CommandBusInterface
{
    private $queue;

    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($command)
    {
        $this->queue->enqueue($command);
    }
}
