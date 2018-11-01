<?php
declare(strict_types = 1);

namespace Innmind\CommandBus;

use Innmind\Reflection\{
    ReflectionObject,
    ReflectionClass,
    ExtractionStrategy\ReflectionStrategy,
};
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

final class Logger implements CommandBus
{
    private $handle;
    private $logger;

    public function __construct(
        CommandBus $handle,
        LoggerInterface $logger
    ) {
        $this->handle = $handle;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(object $command): void
    {
        $reference = (string) Uuid::uuid4();

        $this->logger->info(
            'Command about to be executed',
            [
                'reference' => $reference,
                'class' => get_class($command),
                'data' => $this->extractData($command),
            ]
        );

        ($this->handle)($command);

        $this->logger->info(
            'Command executed',
            ['reference' => $reference]
        );
    }

    private function extractData(object $object): array
    {
        return ReflectionObject::of($object, null, null, new ReflectionStrategy)
            ->extract(...ReflectionClass::of(get_class($object))->properties())
            ->map(function(string $property, $value) {
                if (is_object($value)) {
                    return $this->extractData($value);
                }

                return $value;
            })
            ->reduce(
                [],
                function(array $carry, string $property, $value): array {
                    $carry[$property] = $value;

                    return $carry;
                }
            );
    }
}
