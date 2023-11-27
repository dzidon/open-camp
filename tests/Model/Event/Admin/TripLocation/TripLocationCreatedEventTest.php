<?php

namespace App\Tests\Model\Event\Admin\TripLocation;

use App\Library\Data\Admin\TripLocationData;
use App\Model\Entity\TripLocation;
use App\Model\Entity\TripLocationPath;
use App\Model\Event\Admin\TripLocation\TripLocationCreatedEvent;
use PHPUnit\Framework\TestCase;

class TripLocationCreatedEventTest extends TestCase
{
    private TripLocationPath $tripLocationPath;

    private TripLocation $tripLocation;

    private TripLocationData $data;

    private TripLocationCreatedEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getTripLocationData());

        $newData = new TripLocationData();
        $this->event->setTripLocationData($newData);
        $this->assertSame($newData, $this->event->getTripLocationData());
    }

    public function testEntity(): void
    {
        $this->assertSame($this->tripLocation, $this->event->getTripLocation());

        $newTripLocation = new TripLocation('Location new', 2000.0, 200, $this->tripLocationPath);
        $this->event->setTripLocation($newTripLocation);
        $this->assertSame($newTripLocation, $this->event->getTripLocation());
    }

    protected function setUp(): void
    {
        $this->tripLocationPath = new TripLocationPath('Path');
        $this->tripLocation = new TripLocation('Location', 1000.0, 100, $this->tripLocationPath);
        $this->data = new TripLocationData();
        $this->event = new TripLocationCreatedEvent($this->data, $this->tripLocation);
    }
}