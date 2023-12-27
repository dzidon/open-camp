<?php

namespace App\Tests\Model\Event\Admin\CampDate;

use App\Library\Data\Admin\CampDateData;
use App\Model\Entity\Camp;
use App\Model\Entity\CampDate;
use App\Model\Event\Admin\CampDate\CampDateCreateEvent;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class CampDateCreateEventTest extends TestCase
{
    private Camp $camp;

    private CampDateData $data;

    private CampDateCreateEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getCampDateData());

        $newData = new CampDateData($this->camp);
        $this->event->setCampDateData($newData);
        $this->assertSame($newData, $this->event->getCampDateData());
    }

    public function testIsFlush(): void
    {
        $this->assertTrue($this->event->isFlush());

        $this->event->setIsFlush(false);
        $this->assertFalse($this->event->isFlush());

        $this->event->setIsFlush(true);
        $this->assertTrue($this->event->isFlush());
    }

    public function testEntity(): void
    {
        $this->assertNull($this->event->getCampDate());

        $newEntity = new CampDate(new DateTimeImmutable('2000-01-02'), new DateTimeImmutable('2000-01-08'), 2000, 200.0, 20, $this->camp);
        $this->event->setCampDate($newEntity);
        $this->assertSame($newEntity, $this->event->getCampDate());
    }

    protected function setUp(): void
    {
        $this->camp = new Camp('Camp', 'camp', 5, 10, 'Street 123', 'Town', '12345', 'CS', 321);
        $this->data = new CampDateData($this->camp);
        $this->event = new CampDateCreateEvent($this->data);
    }
}