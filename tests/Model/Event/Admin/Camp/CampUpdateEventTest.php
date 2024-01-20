<?php

namespace App\Tests\Model\Event\Admin\Camp;

use App\Library\Data\Admin\CampData;
use App\Model\Entity\Camp;
use App\Model\Event\Admin\Camp\CampUpdateEvent;
use PHPUnit\Framework\TestCase;

class CampUpdateEventTest extends TestCase
{
    private Camp $entity;

    private CampData $data;

    private CampUpdateEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getCampData());

        $newData = new CampData();
        $this->event->setCampData($newData);
        $this->assertSame($newData, $this->event->getCampData());
    }

    public function testEntity(): void
    {
        $this->assertSame($this->entity, $this->event->getCamp());

        $newEntity = new Camp('Camp new', 'camp-new', 6, 11, 123);
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
        $this->entity = new Camp('Camp', 'camp', 5, 10, 321);
        $this->data = new CampData();
        $this->event = new CampUpdateEvent($this->data, $this->entity);
    }
}