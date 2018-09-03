<?php
declare(strict_types = 1);

namespace Innmind\CommandBus;

use Innmind\CommandBus\Exception\InvalidArgumentException;
use Innmind\Immutable\Sequence;

/**
 * @deprecated See EnqueueCommandBus and DequeueCommandBus
 */
final class QueueableCommandBus implements CommandBusInterface
{
    private $commandBus;
    private $commandQueue;
    private $inHandle = false;

    public function __construct(CommandBusInterface $commandBus)
    {
        trigger_error('See EnqueueCommandBus and DequeueCommandBus', E_USER_DEPRECATED);

        $this->commandBus = $commandBus;
        $this->commandQueue = new Sequence;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($command)
    {
        if (!is_object($command)) {
            throw new InvalidArgumentException;
        }

        if ($this->inHandle) {
            $this->commandQueue = $this->commandQueue->add($command);

            return;
        }

        $this->inHandle = true;

        try {
            $this->commandBus->handle($command);
        } catch (\Throwable $e) {
            $this->commandQueue = new Sequence;
            $this->inHandle = false;
            throw $e;
        }

        $this->inHandle = false;

        $this
            ->commandQueue
            ->foreach(function($command) {
                $this->commandQueue = $this->commandQueue->drop(1);
                $this->handle($command);
            });
    }
}
