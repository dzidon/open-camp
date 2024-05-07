<?php

namespace App\Tests\Model\Event\Admin\CampCategory;

use App\Model\Entity\CampCategory;
use App\Model\Event\Admin\CampCategory\CampCategoryDeleteEvent;
use PHPUnit\Framework\TestCase;

class CampCategoryDeleteEventTest extends TestCase
{
    private CampCategory $entity;

    private CampCategoryDeleteEvent $event;

    public function testEntity(): void
    {
        $this->assertSame($this->entity, $this->event->getCampCategory());

        $newEntity = new CampCategory('Category new', 'category-new', 100);
        $this->event->setCampCategory($newEntity);
        $this->assertSame($newEntity, $this->event->getCampCategory());
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
        $this->entity = new CampCategory('Category', 'category', 100);
        $this->event = new CampCategoryDeleteEvent($this->entity);
    }
}