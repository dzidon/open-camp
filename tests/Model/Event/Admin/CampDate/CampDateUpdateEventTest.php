<?php

namespace App\Tests\Model\Event\Admin\CampDate;

use App\Library\Data\Admin\CampDateData;
use App\Model\Entity\Camp;
use App\Model\Entity\CampDate;
use App\Model\Event\Admin\CampDate\CampDateUpdateEvent;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class CampDateUpdateEventTest extends TestCase
{
    private Camp $camp;

    private CampDate $campDate;

    private CampDateData $data;

    private CampDateUpdateEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getCampDateData());

        $newData = new CampDateData($this->camp);
        $this->event->setCampDateData($newData);
        $this->assertSame($newData, $this->event->getCampDateData());
    }

    public function testEntity(): void
    {
        $this->assertSame($this->campDate, $this->event->getCampDate());

        $newCampDate = new CampDate(new DateTimeImmutable('2000-01-02'), new DateTimeImmutable('2000-01-08'), 2000, 20, $this->camp);
        $this->event->setCampDate($newCampDate);
        $this->assertSame($newCampDate, $this->event->getCampDate());
    }

    protected function setUp(): void
    {
        $this->camp = new Camp('Camp', 'camp', 5, 10, 'Street 123', 'Town', '12345', 'CS', 321);
        $this->campDate = new CampDate(new DateTimeImmutable('2000-01-01'), new DateTimeImmutable('2000-01-07'), 1000, 10, $this->camp);
        $this->data = new CampDateData($this->camp);
        $this->event = new CampDateUpdateEvent($this->data, $this->campDate);
    }
}