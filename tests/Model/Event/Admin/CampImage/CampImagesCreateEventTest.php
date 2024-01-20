<?php

namespace App\Tests\Model\Event\Admin\CampImage;

use App\Library\Data\Admin\CampImagesUploadData;
use App\Model\Entity\Camp;
use App\Model\Entity\CampImage;
use App\Model\Event\Admin\CampImage\CampImagesCreateEvent;
use LogicException;
use PHPUnit\Framework\TestCase;
use stdClass;

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

    public function testCampImages(): void
    {
        $this->assertEmpty($this->event->getCampImages());

        $newCampImages = [new CampImage(300, 'jpg', $this->camp)];
        $this->event->setCampImages($newCampImages);
        $this->assertSame($newCampImages, $this->event->getCampImages());
    }

    public function testInvalidCampImages(): void
    {
        $this->expectException(LogicException::class);

        /** @var CampImage[] $newCampImages */
        $newCampImages = [new stdClass()];
        $this->event->setCampImages($newCampImages);
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
        $this->data = new CampImagesUploadData($this->camp);
        $this->event = new CampImagesCreateEvent($this->data);
    }
}