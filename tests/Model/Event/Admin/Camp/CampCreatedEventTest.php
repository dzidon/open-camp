<?php

namespace App\Tests\Model\Event\Admin\Camp;

use App\Library\Data\Admin\CampCreationData;
use App\Model\Entity\Camp;
use App\Model\Entity\CampImage;
use App\Model\Event\Admin\Camp\CampCreatedEvent;
use LogicException;
use PHPUnit\Framework\TestCase;
use stdClass;

class CampCreatedEventTest extends TestCase
{
    private Camp $entity;

    private CampCreationData $data;

    /**
     * @var CampImage[]
     */
    private array $campImages;

    private CampCreatedEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getCampCreationData());

        $newData = new CampCreationData();
        $this->event->setCampCreationData($newData);
        $this->assertSame($newData, $this->event->getCampCreationData());
    }

    public function testEntity(): void
    {
        $this->assertSame($this->entity, $this->event->getCamp());

        $newEntity = new Camp('Camp new', 'camp-new', 6, 11, 'Street 123', 'Town', '12345', 'CS', 123);
        $this->event->setCamp($newEntity);
        $this->assertSame($newEntity, $this->event->getCamp());
    }

    public function testCampImages(): void
    {
        $this->assertSame($this->campImages, $this->event->getCampImages());

        $newCampImage = new CampImage(300, 'jpg', $this->entity);
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
        new CampCreatedEvent($this->data, $this->entity, [new stdClass()]);
    }

    protected function setUp(): void
    {
        $this->entity = new Camp('Camp', 'camp', 5, 10, 'Street 123', 'Town', '12345', 'CS', 321);
        $this->data = new CampCreationData();
        $this->campImages = [
            new CampImage(100, 'jpg', $this->entity),
            new CampImage(200, 'png', $this->entity),
        ];

        $this->event = new CampCreatedEvent($this->data, $this->entity, $this->campImages);
    }
}