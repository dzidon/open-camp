<?php

namespace App\Service\Logger;

use Psr\Log\LoggerInterface as PsrLoggerInterface;

/**
 * Interface for loggers that log model changes.
 */
interface ModelLoggerInterface extends PsrLoggerInterface
{

}