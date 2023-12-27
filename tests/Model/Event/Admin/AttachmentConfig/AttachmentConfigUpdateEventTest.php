<?php

namespace App\Tests\Model\Event\Admin\AttachmentConfig;

use App\Library\Data\Admin\AttachmentConfigData;
use App\Model\Entity\AttachmentConfig;
use App\Model\Event\Admin\AttachmentConfig\AttachmentConfigUpdateEvent;
use PHPUnit\Framework\TestCase;

class AttachmentConfigUpdateEventTest extends TestCase
{
    private AttachmentConfig $entity;

    private AttachmentConfigData $data;

    private AttachmentConfigUpdateEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getAttachmentConfigData());

        $newData = new AttachmentConfigData();
        $this->event->setAttachmentConfigData($newData);
        $this->assertSame($newData, $this->event->getAttachmentConfigData());
    }

    public function testEntity(): void
    {
        $this->assertSame($this->entity, $this->event->getAttachmentConfig());

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
        $this->entity = new AttachmentConfig('Config', 'Config:', 10.0);
        $this->data = new AttachmentConfigData();
        $this->event = new AttachmentConfigUpdateEvent($this->data, $this->entity);
    }
}