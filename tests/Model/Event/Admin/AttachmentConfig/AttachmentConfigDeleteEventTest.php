<?php

namespace App\Tests\Model\Event\Admin\AttachmentConfig;

use App\Model\Entity\AttachmentConfig;
use App\Model\Event\Admin\AttachmentConfig\AttachmentConfigDeleteEvent;
use PHPUnit\Framework\TestCase;

class AttachmentConfigDeleteEventTest extends TestCase
{
    private AttachmentConfig $entity;

    private AttachmentConfigDeleteEvent $event;

    public function testEntity(): void
    {
        $this->assertSame($this->entity, $this->event->getAttachmentConfig());

        $newEntity = new AttachmentConfig('Config new', 'Config new:', 20.0);
        $this->event->setAttachmentConfig($newEntity);
        $this->assertSame($newEntity, $this->event->getAttachmentConfig());
    }

    protected function setUp(): void
    {
        $this->entity = new AttachmentConfig('Config', 'Config:', 10.0);
        $this->event = new AttachmentConfigDeleteEvent($this->entity);
    }
}