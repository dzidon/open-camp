<?php

namespace App\Tests\Model\Event\Admin\AttachmentConfig;

use App\Library\Data\Admin\AttachmentConfigData;
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

    protected function setUp(): void
    {
        $this->data = new AttachmentConfigData();
        $this->event = new AttachmentConfigCreateEvent($this->data);
    }
}