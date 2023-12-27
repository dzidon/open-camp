<?php

namespace App\Tests\Model\Event\Admin\AttachmentConfig;

use App\Library\Data\Admin\AttachmentConfigData;
use App\Model\Entity\AttachmentConfig;
use App\Model\Event\Admin\AttachmentConfig\AttachmentConfigCreateEvent;
use PHPUnit\Framework\TestCase;

class AttachmentConfigCreateEventTest extends TestCase
{
    private AttachmentConfigData $data;

    private AttachmentConfigCreateEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getAttachmentConfigData());

        $newData = new AttachmentConfigData();
        $this->event->setAttachmentConfigData($newData);
        $this->assertSame($newData, $this->event->getAttachmentConfigData());
    }

    public function testEntity(): void
    {
        $this->assertNull($this->event->getAttachmentConfig());

        $newEntity = new AttachmentConfig('Config new', 'Config new:', 20.0);
        $this->event->setAttachmentConfig($newEntity);
        $this->assertSame($newEntity, $this->event->getAttachmentConfig());
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
        $this->data = new AttachmentConfigData();
        $this->event = new AttachmentConfigCreateEvent($this->data);
    }
}