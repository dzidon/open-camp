<?php

namespace App\Tests\Model\Event\Admin\TripLocation;

use App\Library\Data\Admin\TripLocationData;
use App\Model\Entity\TripLocation;
use App\Model\Entity\TripLocationPath;
use App\Model\Event\Admin\TripLocation\TripLocationUpdateEvent;
use PHPUnit\Framework\TestCase;

class TripLocationUpdateEventTest extends TestCase
{
    private TripLocationPath $tripLocationPath;

    private TripLocation $tripLocation;

    private TripLocationData $data;

    private TripLocationUpdateEvent $event;

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
        $this->tripLocationPath = new TripLocationPath('Path');
        $this->tripLocation = new TripLocation('Location', 1000.0, 100, $this->tripLocationPath);
        $this->data = new TripLocationData();
        $this->event = new TripLocationUpdateEvent($this->data, $this->tripLocation);
    }
}