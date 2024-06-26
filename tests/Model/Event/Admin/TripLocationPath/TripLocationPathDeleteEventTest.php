<?php

namespace App\Tests\Model\Event\Admin\TripLocationPath;

use App\Model\Entity\TripLocationPath;
use App\Model\Event\Admin\TripLocationPath\TripLocationPathDeleteEvent;
use PHPUnit\Framework\TestCase;

class TripLocationPathDeleteEventTest extends TestCase
{
    private TripLocationPath $entity;

    private TripLocationPathDeleteEvent $event;

    public function testEntity(): void
    {
        $this->assertSame($this->entity, $this->event->getTripLocationPath());

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
        $this->entity = new TripLocationPath('Path');
        $this->event = new TripLocationPathDeleteEvent($this->entity);
    }
}