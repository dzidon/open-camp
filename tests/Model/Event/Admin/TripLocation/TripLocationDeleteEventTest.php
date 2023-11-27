<?php

namespace App\Tests\Model\Event\Admin\TripLocation;

use App\Model\Entity\TripLocation;
use App\Model\Entity\TripLocationPath;
use App\Model\Event\Admin\TripLocation\TripLocationDeleteEvent;
use PHPUnit\Framework\TestCase;

class TripLocationDeleteEventTest extends TestCase
{
    private TripLocationPath $tripLocationPath;

    private TripLocation $tripLocation;

    private TripLocationDeleteEvent $event;

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
        $this->event = new TripLocationDeleteEvent($this->tripLocation);
    }
}