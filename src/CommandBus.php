<?php
declare(strict_types = 1);

namespace Innmind\CommandBus;

use Innmind\Immutable\{
    Either,
    SideEffect,
};

interface CommandBus
{
    /**
     * @template E
     * @template H of Handler<E>
     *
     * @param Command<H> $command
     *
     * @return Either<E, SideEffect>
     */
    public function __invoke(Command $command): Either;
}
