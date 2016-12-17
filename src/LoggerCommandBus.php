<?php
declare(strict_types = 1);

namespace Innmind\CommandBus;

use Innmind\CommandBus\Exception\InvalidArgumentException;
use Innmind\Reflection\ReflectionObject as InnmindReflectionObject;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

final class LoggerCommandBus implements CommandBusInterface
{
    private $commandBus;
    private $logger;

    public function __construct(
        CommandBusInterface $commandBus,
        LoggerInterface $logger
    ) {
        $this->commandBus = $commandBus;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($command)
    {
        if (!is_object($command)) {
            throw new InvalidArgumentException;
        }

        $reference = (string) Uuid::uuid4();

        $this->logger->info(
            'Command about to be executed',
            [
                'reference' => $reference,
                'class' => get_class($command),
                'data' => $this->extractData($command),
            ]
        );
        $this->commandBus->handle($command);
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

        $data = (new InnmindReflectionObject($object))
            ->extract($properties)
            ->toPrimitive();

        foreach ($data as $key => $value) {
            if (is_object($value)) {
                $data[$key] = $this->extractData($value);
            }
        }

        return $data;
    }
}
