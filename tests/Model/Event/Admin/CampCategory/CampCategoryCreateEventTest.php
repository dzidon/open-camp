<?php

namespace App\Tests\Model\Event\Admin\CampCategory;

use App\Library\Data\Admin\CampCategoryData;
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

    protected function setUp(): void
    {
        $this->data = new CampCategoryData();
        $this->event = new CampCategoryCreateEvent($this->data);
    }
}