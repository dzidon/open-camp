<?php

namespace App\Tests\Model\Event\User\Camper;

use App\Library\Data\User\CamperData;
use App\Library\Enum\GenderEnum;
use App\Model\Entity\Camper;
use App\Model\Entity\User;
use App\Model\Event\User\Camper\CamperCreateEvent;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class CamperCreateEventTest extends TestCase
{
    private User $user;

    private CamperData $data;

    private CamperCreateEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getCamperData());

        $newData = new CamperData(true);
        $this->event->setCamperData($newData);
        $this->assertSame($newData, $this->event->getCamperData());
    }

    public function testUser(): void
    {
        $this->assertSame($this->user, $this->event->getUser());

        $newEntity = new User('bob.new@gmail.com');
        $this->event->setUser($newEntity);
        $this->assertSame($newEntity, $this->event->getUser());
    }

    public function testCamper(): void
    {
        $this->assertNull($this->event->getCamper());

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
        $this->data = new CamperData(true);
        $this->event = new CamperCreateEvent($this->data, $this->user);
    }
}