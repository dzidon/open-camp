<?php

namespace App\Tests\Model\Event\Admin\CampDate;

use App\Model\Entity\Camp;
use App\Model\Entity\CampDate;
use App\Model\Event\Admin\CampDate\CampDateDeleteEvent;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class CampDateDeleteEventTest extends TestCase
{
    private Camp $camp;

    private CampDate $campDate;

    private CampDateDeleteEvent $event;

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
        $this->event = new CampDateDeleteEvent($this->campDate);
    }
}