<?php
declare(strict_types = 1);

namespace Innmind\CommandBus;

final class Queue
{
    /** @var \SplQueue<object> */
    private \SplQueue $queue;

    public function __construct()
    {
        /** @var \SplQueue<object> */
        $this->queue = new \SplQueue;
        $this->queue->setIteratorMode(\SplDoublyLinkedList::IT_MODE_FIFO | \SplDoublyLinkedList::IT_MODE_DELETE);
    }

    public function enqueue(object $event): void
    {
        $this->queue->enqueue($event);
    }

    public function dequeue(): object
    {
        return $this->queue->dequeue();
    }

    public function valid(): bool
    {
        return !$this->queue->isEmpty();
    }
}
