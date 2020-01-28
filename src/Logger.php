<?php
declare(strict_types = 1);

namespace Innmind\CommandBus;

use Innmind\Reflection\{
    ReflectionObject,
    ReflectionClass,
    ExtractionStrategy\ReflectionStrategy,
};
use function Innmind\Immutable\unwrap;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

final class Logger implements CommandBus
{
    private CommandBus $handle;
    private LoggerInterface $logger;

    public function __construct(
        CommandBus $handle,
        LoggerInterface $logger
    ) {
        $this->handle = $handle;
        $this->logger = $logger;
    }

    public function __invoke(object $command): void
    {
        $reference = Uuid::uuid4()->toString();

        $this->logger->info(
            'Command about to be executed',
            [
                'reference' => $reference,
                'class' => \get_class($command),
                'data' => $this->extractData($command),
            ],
        );

        ($this->handle)($command);

        $this->logger->info(
            'Command executed',
            ['reference' => $reference],
        );
    }

    private function extractData(object $object): array
    {
        /**
         * @psalm-suppress MissingClosureReturnType
         */
        return ReflectionObject::of($object, null, null, new ReflectionStrategy)
            ->extract(...unwrap(ReflectionClass::of(\get_class($object))->properties()))
            ->map(function(string $property, $value) {
                if (\is_object($value)) {
                    return $this->extractData($value);
                }

                return $value;
            })
            ->reduce(
                [],
                function(array $carry, string $property, $value): array {
                    /** @psalm-suppress MixedAssignment */
                    $carry[$property] = $value;

                    return $carry;
                },
            );
    }
}
