<?php
declare(strict_types = 1);

namespace Innmind\CommandBus;

use Innmind\Immutable\{
    Either,
    SideEffect,
};

final class NullCommandBus implements CommandBus
{
    public function __invoke(Command $command): Either
    {
        return Either::right(new SideEffect);
    }
}
