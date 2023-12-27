<?php

namespace App\Tests\Model\Event\Admin\Camp;

use App\Library\Data\Admin\CampCreationData;
use App\Model\Entity\Camp;
use App\Model\Event\Admin\Camp\CampCreateEvent;
use PHPUnit\Framework\TestCase;

class CampCreateEventTest extends TestCase
{
    private CampCreationData $data;

    private CampCreateEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getCampCreationData());

        $newData = new CampCreationData();
        $this->event->setCampCreationData($newData);
        $this->assertSame($newData, $this->event->getCampCreationData());
    }

    public function testEntity(): void
    {
        $this->assertNull($this->event->getCamp());

        $newEntity = new Camp('Camp new', 'camp-new', 6, 11, 'Street 123', 'Town', '12345', 'CS', 123);
        $this->event->setCamp($newEntity);
        $this->assertSame($newEntity, $this->event->getCamp());
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
        $this->data = new CampCreationData();
        $this->event = new CampCreateEvent($this->data);
    }
}