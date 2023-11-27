<?php

namespace App\Tests\Model\Event\Admin\CampImage;

use App\Model\Entity\Camp;
use App\Model\Entity\CampImage;
use App\Model\Event\Admin\CampImage\CampImageDeleteEvent;
use PHPUnit\Framework\TestCase;

class CampImageDeleteEventTest extends TestCase
{
    private Camp $camp;

    private CampImage $campImage;

    private CampImageDeleteEvent $event;

    public function testEntity(): void
    {
        $this->assertSame($this->campImage, $this->event->getCampImage());

        $newCampImage = new CampImage(200, 'png', $this->camp);
        $this->event->setCampImage($newCampImage);
        $this->assertSame($newCampImage, $this->event->getCampImage());
    }

    protected function setUp(): void
    {
        $this->camp = new Camp('Camp', 'camp', 5, 10, 'Street 123', 'Town', '12345', 'CS', 321);
        $this->campImage = new CampImage(100, 'jpg', $this->camp);
        $this->event = new CampImageDeleteEvent($this->campImage);
    }
}