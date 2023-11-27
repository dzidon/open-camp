<?php

namespace App\Tests\Model\Event\User\Camper;

use App\Library\Data\User\CamperData;
use App\Library\Enum\GenderEnum;
use App\Model\Entity\Camper;
use App\Model\Entity\User;
use App\Model\Event\User\Camper\CamperCreatedEvent;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class CamperCreatedEventTest extends TestCase
{
    private Camper $camper;

    private User $user;

    private CamperData $data;

    private CamperCreatedEvent $event;

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

    protected function setUp(): void
    {
        $this->user = new User('bob@gmail.com');
        $this->camper = new Camper('Bob', 'Smith', GenderEnum::MALE, new DateTimeImmutable(), $this->user);
        $this->data = new CamperData(true);
        $this->event = new CamperCreatedEvent($this->data, $this->camper);
    }
}