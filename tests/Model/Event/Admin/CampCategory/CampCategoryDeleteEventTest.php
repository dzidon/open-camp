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

        $newEntity = new CampCategory('Category new', 'category-new');
        $this->event->setCampCategory($newEntity);
        $this->assertSame($newEntity, $this->event->getCampCategory());
    }

    protected function setUp(): void
    {
        $this->entity = new CampCategory('Category', 'category');
        $this->event = new CampCategoryDeleteEvent($this->entity);
    }
}