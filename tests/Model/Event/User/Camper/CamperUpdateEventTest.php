<?php

namespace App\Tests\Model\Event\User\Camper;

use App\Library\Data\Common\CamperData;
use App\Library\Enum\GenderEnum;
use App\Model\Entity\Camper;
use App\Model\Entity\User;
use App\Model\Event\User\Camper\CamperUpdateEvent;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class CamperUpdateEventTest extends TestCase
{
    private Camper $camper;

    private CamperData $data;

    private CamperUpdateEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getCamperData());

        $newData = new CamperData(true);
        $this->event->setCamperData($newData);
        $this->assertSame($newData, $this->event->getCamperData());
    }

    public function testEntity(): void
    {
        $this->assertSame($this->camper, $this->event->getCamper());

        $newCamper = new Camper('Sam', 'Doe', GenderEnum::MALE, new DateTimeImmutable(), $this->user);
        $this->event->setCamper($newCamper);
        $this->assertSame($newCamper, $this->event->getCamper());
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
        $this->user = new User('bob@gmail.com');
        $this->camper = new Camper('Bob', 'Smith', GenderEnum::MALE, new DateTimeImmutable(), $this->user);
        $this->data = new CamperData(true);
        $this->event = new CamperUpdateEvent($this->data, $this->camper);
    }
}