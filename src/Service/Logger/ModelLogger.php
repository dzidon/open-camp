<?php

namespace App\Service\Logger;

use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @inheritDoc
 */
class ModelLogger extends AbstractLogger implements ModelLoggerInterface
{
    public function __construct(
        #[Autowire('@monolog.logger.model')]
        PsrLoggerInterface $logger,
        SerializerInterface $serializer
    ) {
        parent::__construct($logger, $serializer);
    }
}