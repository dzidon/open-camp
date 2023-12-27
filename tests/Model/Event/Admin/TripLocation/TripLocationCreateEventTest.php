<?php

namespace App\Tests\Model\Event\Admin\TripLocation;

use App\Library\Data\Admin\TripLocationData;
use App\Model\Entity\TripLocation;
use App\Model\Entity\TripLocationPath;
use App\Model\Event\Admin\TripLocation\TripLocationCreateEvent;
use PHPUnit\Framework\TestCase;

class TripLocationCreateEventTest extends TestCase
{
    private TripLocationPath $tripLocationPath;

    private TripLocationData $data;

    private TripLocationCreateEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getTripLocationData());

        $newData = new TripLocationData();
        $this->event->setTripLocationData($newData);
        $this->assertSame($newData, $this->event->getTripLocationData());
    }

    public function testTripLocationPath(): void
    {
        $this->assertSame($this->tripLocationPath, $this->event->getTripLocationPath());

        $newTripLocationPath = new TripLocationPath('Path');
        $this->event->setTripLocationPath($newTripLocationPath);
        $this->assertSame($newTripLocationPath, $this->event->getTripLocationPath());
    }

    public function testTripLocation(): void
    {
        $this->assertNull($this->event->getTripLocation());

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
        $this->data = new TripLocationData();
        $this->tripLocationPath = new TripLocationPath('Path');
        $this->event = new TripLocationCreateEvent($this->data, $this->tripLocationPath);
    }
}