<?php

namespace App\Tests\Service\Logger;

use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Psr\Log\LogLevel;
use Stringable;

class LoggerMock implements PsrLoggerInterface
{
    private array $loggedMessages = [];

    public function hasLoggedMessage(string $level, array $message): bool
    {
        if (!array_key_exists($level, $this->loggedMessages))
        {
            return false;
        }

        return in_array($message, $this->loggedMessages[$level]);
    }

    public function emergency(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    public function alert(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    public function critical(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    public function error(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    public function warning(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    public function notice(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    public function info(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    public function debug(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    public function log($level, Stringable|string $message, array $context = []): void
    {
        $this->loggedMessages[$level][] = [
            'message' => (string) $message,
            'context' => $context,
        ];
    }
}