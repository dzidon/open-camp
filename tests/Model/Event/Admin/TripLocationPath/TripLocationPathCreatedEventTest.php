<?php

namespace App\Tests\Model\Event\Admin\TripLocationPath;

use App\Library\Data\Admin\TripLocationPathCreationData;
use App\Model\Entity\TripLocation;
use App\Model\Entity\TripLocationPath;
use App\Model\Event\Admin\TripLocationPath\TripLocationPathCreatedEvent;
use LogicException;
use PHPUnit\Framework\TestCase;
use stdClass;

class TripLocationPathCreatedEventTest extends TestCase
{
    private TripLocationPath $tripLocationPath;

    private array $tripLocations;

    private TripLocationPathCreationData $data;

    private TripLocationPathCreatedEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getTripLocationPathCreationData());

        $newData = new TripLocationPathCreationData();
        $this->event->setTripLocationPathCreationData($newData);
        $this->assertSame($newData, $this->event->getTripLocationPathCreationData());
    }

    public function testEntity(): void
    {
        $this->assertSame($this->tripLocationPath, $this->event->getTripLocationPath());

        $newEntity = new TripLocationPath('Path new');
        $this->event->setTripLocationPath($newEntity);
        $this->assertSame($newEntity, $this->event->getTripLocationPath());
    }

    public function testTripLocations(): void
    {
        $this->assertSame($this->tripLocations, $this->event->getTripLocations());

        $newTripLocation = new TripLocation('Location 3', 3000.0, 300, $this->tripLocationPath);
        $this->event->addTripLocation($newTripLocation);

        $tripLocations = $this->event->getTripLocations();
        $this->assertContains($newTripLocation, $tripLocations);

        $this->event->removeTripLocation($newTripLocation);
        $tripLocations = $this->event->getTripLocations();
        $this->assertNotContains($newTripLocation, $tripLocations);
    }

    public function testInvalidTripLocations(): void
    {
        $this->expectException(LogicException::class);
        new TripLocationPathCreatedEvent($this->data, $this->tripLocationPath, [new stdClass()]);
    }

    protected function setUp(): void
    {
        $this->tripLocationPath = new TripLocationPath('Path');
        $this->tripLocations = [
            new TripLocation('Location 1', 1000.0, 100, $this->tripLocationPath),
            new TripLocation('Location 2', 2000.0, 200, $this->tripLocationPath),
        ];
        $this->data = new TripLocationPathCreationData();
        $this->event = new TripLocationPathCreatedEvent($this->data, $this->tripLocationPath, $this->tripLocations);
    }
}