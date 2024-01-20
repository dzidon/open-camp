<?php

namespace App\Tests\Model\Event\Admin\CampImage;

use App\Library\Data\Admin\CampImageData;
use App\Model\Entity\Camp;
use App\Model\Entity\CampImage;
use App\Model\Event\Admin\CampImage\CampImageUpdateEvent;
use PHPUnit\Framework\TestCase;

class CampImageUpdateEventTest extends TestCase
{
    private Camp $camp;

    private CampImage $campImage;

    private CampImageData $data;

    private CampImageUpdateEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getCampImageData());

        $newData = new CampImageData();
        $this->event->setCampImageData($newData);
        $this->assertSame($newData, $this->event->getCampImageData());
    }

    public function testEntity(): void
    {
        $this->assertSame($this->campImage, $this->event->getCampImage());

        $newCampImage = new CampImage(200, 'jpg', $this->camp);
        $this->event->setCampImage($newCampImage);
        $this->assertSame($newCampImage, $this->event->getCampImage());
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
        $this->camp = new Camp('Camp', 'camp', 5, 10, 321);
        $this->campImage = new CampImage(100, 'png', $this->camp);
        $this->data = new CampImageData();
        $this->event = new CampImageUpdateEvent($this->data, $this->campImage);
    }
}