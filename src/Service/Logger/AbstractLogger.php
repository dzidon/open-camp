<?php

namespace App\Service\Logger;

use Psr\Log\LogLevel;
use Stringable;
use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Abstraction for other loggers.
 */
abstract class AbstractLogger implements PsrLoggerInterface
{
    private PsrLoggerInterface $logger;
    private SerializerInterface $serializer;

    public function __construct(PsrLoggerInterface $logger, SerializerInterface $serializer)
    {
        $this->logger = $logger;
        $this->serializer = $serializer;
    }

    /**
     * @inheritDoc
     */
    public function emergency(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function alert(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function critical(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function error(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function warning(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function notice(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function info(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function debug(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function log(mixed $level, string|Stringable $message, array $context = []): void
    {
        foreach ($context as &$item)
        {
            if (is_object($item) || is_iterable($item))
            {
                $item = $this->serializer->serialize($item, 'json', [
                    AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function () {
                        return null;
                    },
                ]);
            }
        }

        $this->logger->log($level, $message, $context);
    }
}