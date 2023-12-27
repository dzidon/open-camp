<?php

namespace App\Tests\Model\Event\Admin\AttachmentConfig;

use App\Model\Entity\AttachmentConfig;
use App\Model\Entity\FileExtension;
use App\Model\Event\Admin\AttachmentConfig\AttachmentConfigFileExtensionCreateEvent;
use PHPUnit\Framework\TestCase;

class AttachmentConfigFileExtensionCreateEventTest extends TestCase
{
    private AttachmentConfig $attachmentConfig;

    private string $extension = 'pdf';

    private AttachmentConfigFileExtensionCreateEvent $event;

    public function testAttachmentConfig(): void
    {
        $this->assertSame($this->attachmentConfig, $this->event->getAttachmentConfig());

        $newEntity = new AttachmentConfig('Config new', 'Config new:', 20.0);
        $this->event->setAttachmentConfig($newEntity);
        $this->assertSame($newEntity, $this->event->getAttachmentConfig());
    }

    public function testExtension(): void
    {
        $this->assertSame($this->extension, $this->event->getExtension());

        $newExtension = 'jpg';
        $this->event->setExtension($newExtension);
        $this->assertSame($newExtension, $this->event->getExtension());
    }

    public function testFileExtension(): void
    {
        $this->assertNull($this->event->getFileExtension());

        $newEntity = new FileExtension('pdf');
        $this->event->setFileExtension($newEntity);
        $this->assertSame($newEntity, $this->event->getFileExtension());
    }

    public function testIsFlush(): void
    {
        $this->assertTrue($this->event->isFlush());

        $this->event->setIsFlush(false);
        $this->assertFalse($this->event->isFlush());

        $this->event->setIsFlush(true);
        $this->assertTrue($this->event->isFlush());
    }

    protected function setUp(): void
    {
        $this->attachmentConfig = new AttachmentConfig('Config', 'Config:', 10.0);
        $this->event = new AttachmentConfigFileExtensionCreateEvent($this->attachmentConfig, $this->extension);
    }
}