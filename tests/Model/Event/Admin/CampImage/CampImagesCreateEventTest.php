<?php

namespace App\Tests\Model\Event\Admin\CampImage;

use App\Library\Data\Admin\CampImagesUploadData;
use App\Model\Entity\Camp;
use App\Model\Event\Admin\CampImage\CampImagesCreateEvent;
use PHPUnit\Framework\TestCase;

class CampImagesCreateEventTest extends TestCase
{
    private Camp $camp;

    private CampImagesUploadData $data;

    private CampImagesCreateEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getCampImagesUploadData());

        $newData = new CampImagesUploadData($this->camp);
        $this->event->setCampImagesUploadData($newData);
        $this->assertSame($newData, $this->event->getCampImagesUploadData());
    }

    protected function setUp(): void
    {
        $this->camp = new Camp('Camp', 'camp', 5, 10, 'Street 123', 'Town', '12345', 'CS', 321);
        $this->data = new CampImagesUploadData($this->camp);
        $this->event = new CampImagesCreateEvent($this->data);
    }
}