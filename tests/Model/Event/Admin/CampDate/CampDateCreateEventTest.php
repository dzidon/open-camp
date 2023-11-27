<?php

namespace App\Tests\Model\Event\Admin\CampDate;

use App\Library\Data\Admin\CampDateData;
use App\Model\Entity\Camp;
use App\Model\Event\Admin\CampDate\CampDateCreateEvent;
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

    protected function setUp(): void
    {
        $this->camp = new Camp('Camp', 'camp', 5, 10, 'Street 123', 'Town', '12345', 'CS', 321);
        $this->data = new CampDateData($this->camp);
        $this->event = new CampDateCreateEvent($this->data);
    }
}