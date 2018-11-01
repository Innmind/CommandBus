<?php
declare(strict_types = 1);

namespace Innmind\CommandBus;

use Innmind\Reflection\{
    ReflectionObject as InnmindReflectionObject,
    ExtractionStrategy\ReflectionStrategy,
};
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

final class LoggerCommandBus implements CommandBus
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

    /**
     * @param object $object
     */
    private function extractData($object): array
    {
        $refl = new \ReflectionObject($object);
        $properties = [];

        foreach ($refl->getProperties() as $property) {
            $properties[] = $property->getName();
        }

        return (new InnmindReflectionObject(
            $object,
            null,
            null,
            new ReflectionStrategy
        ))
            ->extract($properties)
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
