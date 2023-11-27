<?php

namespace App\Tests\Service\Logger;

use App\Service\Logger\ModelLogger;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Serializer\SerializerInterface;

class ModelLoggerTest extends KernelTestCase
{
    private ModelLogger $modelLogger;

    private LoggerMock $loggerMock;

    public function testEmergency(): void
    {
        $this->modelLogger->emergency('Message...', ['a' => 1, 'b' => 2]);

        $this->assertTrue($this->loggerMock->hasLoggedMessage('emergency', [
            'message' => 'Message...',
            'context' => ['a' => 1, 'b' => 2],
        ]));
    }

    public function testAlert(): void
    {
        $this->modelLogger->alert('Message...', ['a' => 1, 'b' => 2]);

        $this->assertTrue($this->loggerMock->hasLoggedMessage('alert', [
            'message' => 'Message...',
            'context' => ['a' => 1, 'b' => 2],
        ]));
    }

    public function testCritical(): void
    {
        $this->modelLogger->critical('Message...', ['a' => 1, 'b' => 2]);

        $this->assertTrue($this->loggerMock->hasLoggedMessage('critical', [
            'message' => 'Message...',
            'context' => ['a' => 1, 'b' => 2],
        ]));
    }

    public function testError(): void
    {
        $this->modelLogger->error('Message...', ['a' => 1, 'b' => 2]);

        $this->assertTrue($this->loggerMock->hasLoggedMessage('error', [
            'message' => 'Message...',
            'context' => ['a' => 1, 'b' => 2],
        ]));
    }

    public function testWarning(): void
    {
        $this->modelLogger->warning('Message...', ['a' => 1, 'b' => 2]);

        $this->assertTrue($this->loggerMock->hasLoggedMessage('warning', [
            'message' => 'Message...',
            'context' => ['a' => 1, 'b' => 2],
        ]));
    }

    public function testNotice(): void
    {
        $this->modelLogger->notice('Message...', ['a' => 1, 'b' => 2]);

        $this->assertTrue($this->loggerMock->hasLoggedMessage('notice', [
            'message' => 'Message...',
            'context' => ['a' => 1, 'b' => 2],
        ]));
    }

    public function testInfo(): void
    {
        $this->modelLogger->info('Message...', ['a' => 1, 'b' => 2]);

        $this->assertTrue($this->loggerMock->hasLoggedMessage('info', [
            'message' => 'Message...',
            'context' => ['a' => 1, 'b' => 2],
        ]));
    }

    public function testDebug(): void
    {
        $this->modelLogger->debug('Message...', ['a' => 1, 'b' => 2]);

        $this->assertTrue($this->loggerMock->hasLoggedMessage('debug', [
            'message' => 'Message...',
            'context' => ['a' => 1, 'b' => 2],
        ]));
    }

    public function testLog(): void
    {
        $this->modelLogger->log('level', 'Message...', ['a' => 1, 'b' => 2]);

        $this->assertTrue($this->loggerMock->hasLoggedMessage('level', [
            'message' => 'Message...',
            'context' => ['a' => 1, 'b' => 2],
        ]));
    }

    protected function setUp(): void
    {
        $container = static::getContainer();

        /** @var SerializerInterface $serializer */
        $serializer = $container->get(SerializerInterface::class);

        $this->loggerMock = new LoggerMock();
        $this->modelLogger = new ModelLogger($this->loggerMock, $serializer);
    }
}