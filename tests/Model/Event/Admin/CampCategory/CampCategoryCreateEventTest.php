<?php

namespace App\Tests\Model\Event\Admin\CampCategory;

use App\Library\Data\Admin\CampCategoryData;
use App\Model\Entity\CampCategory;
use App\Model\Event\Admin\CampCategory\CampCategoryCreateEvent;
use PHPUnit\Framework\TestCase;

class CampCategoryCreateEventTest extends TestCase
{
    private CampCategoryData $data;

    private CampCategoryCreateEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getCampCategoryData());

        $newData = new CampCategoryData();
        $this->event->setCampCategoryData($newData);
        $this->assertSame($newData, $this->event->getCampCategoryData());
    }

    public function testEntity(): void
    {
        $this->assertNull($this->event->getCampCategory());

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
        $this->data = new CampCategoryData();
        $this->event = new CampCategoryCreateEvent($this->data);
    }
}