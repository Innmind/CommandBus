<?php
declare(strict_types = 1);

namespace Innmind\CommandBus;

final class DequeueCommandBus implements CommandBusInterface
{
    private $bus;
    private $queue;

    public function __construct(CommandBusInterface $bus, Queue $queue)
    {
        $this->bus = $bus;
        $this->queue = $queue;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($command)
    {
        $this->bus->handle($command);

        while ($this->queue->valid()) {
            $this->bus->handle($this->queue->dequeue());
        }
    }
}
