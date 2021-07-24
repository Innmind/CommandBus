<?php
declare(strict_types = 1);

namespace Innmind\CommandBus;

use Innmind\Immutable\{
    Either,
    SideEffect,
};

/**
 * @template E
 */
interface Handler
{
    /**
     * @return Either<E, SideEffect>
     */
    public function __invoke(Command $command): Either;
}
