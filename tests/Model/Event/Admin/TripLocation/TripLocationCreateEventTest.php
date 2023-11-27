<?php

namespace App\Tests\Model\Event\Admin\TripLocation;

use App\Library\Data\Admin\TripLocationData;
use App\Model\Event\Admin\TripLocation\TripLocationCreateEvent;
use PHPUnit\Framework\TestCase;

class TripLocationCreateEventTest extends TestCase
{
    private TripLocationData $data;

    private TripLocationCreateEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getTripLocationData());

        $newData = new TripLocationData();
        $this->event->setTripLocationData($newData);
        $this->assertSame($newData, $this->event->getTripLocationData());
    }

    protected function setUp(): void
    {
        $this->data = new TripLocationData();
        $this->event = new TripLocationCreateEvent($this->data);
    }
}