<?php

namespace App\Tests\Model\Event\Admin\TripLocationPath;

use App\Library\Data\Admin\TripLocationPathCreationData;
use App\Model\Event\Admin\TripLocationPath\TripLocationPathCreateEvent;
use PHPUnit\Framework\TestCase;

class TripLocationPathCreateEventTest extends TestCase
{
    private TripLocationPathCreationData $data;

    private TripLocationPathCreateEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getTripLocationPathCreationData());

        $newData = new TripLocationPathCreationData();
        $this->event->setTripLocationPathCreationData($newData);
        $this->assertSame($newData, $this->event->getTripLocationPathCreationData());
    }

    protected function setUp(): void
    {
        $this->data = new TripLocationPathCreationData();
        $this->event = new TripLocationPathCreateEvent($this->data);
    }
}