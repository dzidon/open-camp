<?php

namespace App\Tests\Model\Event\Admin\CampImage;

use App\Library\Data\Admin\CampImagesUploadData;
use App\Model\Entity\Camp;
use App\Model\Entity\CampImage;
use App\Model\Event\Admin\CampImage\CampImagesCreatedEvent;
use LogicException;
use PHPUnit\Framework\TestCase;
use stdClass;

class CampImagesCreatedEventTest extends TestCase
{
    private Camp $camp;

    /**
     * @var CampImage[]
     */
    private array $campImages;

    private CampImagesUploadData $data;

    private CampImagesCreatedEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getCampImagesUploadData());

        $newData = new CampImagesUploadData($this->camp);
        $this->event->setCampImagesUploadData($newData);
        $this->assertSame($newData, $this->event->getCampImagesUploadData());
    }

    public function testCampImages(): void
    {
        $this->assertSame($this->campImages, $this->event->getCampImages());

        $newCampImage = new CampImage(300, 'jpg', $this->camp);
        $this->event->addCampImage($newCampImage);

        $campImages = $this->event->getCampImages();
        $this->assertContains($newCampImage, $campImages);

        $this->event->removeCampImage($newCampImage);
        $campImages = $this->event->getCampImages();
        $this->assertNotContains($newCampImage, $campImages);
    }

    public function testInvalidCampImages(): void
    {
        $this->expectException(LogicException::class);
        new CampImagesCreatedEvent($this->data, [new stdClass()]);
    }

    protected function setUp(): void
    {
        $this->camp = new Camp('Camp', 'camp', 5, 10, 'Street 123', 'Town', '12345', 'CS', 321);
        $this->campImages = [
            new CampImage(100, 'png', $this->camp),
            new CampImage(200, 'jpg', $this->camp),
        ];
        $this->data = new CampImagesUploadData($this->camp);
        $this->event = new CampImagesCreatedEvent($this->data, $this->campImages);
    }
}