<?php

namespace App\Tests\Model\Event\Admin\TripLocationPath;

use App\Library\Data\Admin\TripLocationPathCreationData;
use App\Model\Entity\TripLocationPath;
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

    public function testEntity(): void
    {
        $this->assertNull($this->event->getTripLocationPath());

        $newEntity = new TripLocationPath('Path new');
        $this->event->setTripLocationPath($newEntity);
        $this->assertSame($newEntity, $this->event->getTripLocationPath());
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
        $this->data = new TripLocationPathCreationData();
        $this->event = new TripLocationPathCreateEvent($this->data);
    }
}