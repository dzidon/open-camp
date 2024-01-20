<?php

namespace App\Tests\Model\Event\Admin\Camp;

use App\Model\Entity\Camp;
use App\Model\Event\Admin\Camp\CampDeleteEvent;
use PHPUnit\Framework\TestCase;

class CampDeleteEventTest extends TestCase
{
    private Camp $entity;

    private CampDeleteEvent $event;

    public function testEntity(): void
    {
        $this->assertSame($this->entity, $this->event->getCamp());

        $newEntity = new Camp('Camp', 'camp', 5, 10, 321);
        $this->event->setCamp($newEntity);
        $this->assertSame($newEntity, $this->event->getCamp());
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
        $this->entity = new Camp('Camp', 'camp', 5, 10, 321);
        $this->event = new CampDeleteEvent($this->entity);
    }
}