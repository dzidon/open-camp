<?php

namespace App\Tests\Model\Event\Admin\TripLocationPath;

use App\Library\Data\Admin\TripLocationPathData;
use App\Model\Entity\TripLocationPath;
use App\Model\Event\Admin\TripLocationPath\TripLocationPathUpdateEvent;
use PHPUnit\Framework\TestCase;

class TripLocationPathUpdateEventTest extends TestCase
{
    private TripLocationPath $entity;

    private TripLocationPathData $data;

    private TripLocationPathUpdateEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getTripLocationPathData());

        $newData = new TripLocationPathData();
        $this->event->setTripLocationPathData($newData);
        $this->assertSame($newData, $this->event->getTripLocationPathData());
    }

    public function testEntity(): void
    {
        $this->assertSame($this->entity, $this->event->getTripLocationPath());

        $newEntity = new TripLocationPath('Path new');
        $this->event->setTripLocationPath($newEntity);
        $this->assertSame($newEntity, $this->event->getTripLocationPath());
    }

    protected function setUp(): void
    {
        $this->entity = new TripLocationPath('Path');
        $this->data = new TripLocationPathData();
        $this->event = new TripLocationPathUpdateEvent($this->data, $this->entity);
    }
}