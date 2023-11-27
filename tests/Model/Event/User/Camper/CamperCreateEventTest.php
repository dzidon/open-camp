<?php

namespace App\Tests\Model\Event\User\Camper;

use App\Library\Data\User\CamperData;
use App\Model\Entity\User;
use App\Model\Event\User\Camper\CamperCreateEvent;
use PHPUnit\Framework\TestCase;

class CamperCreateEventTest extends TestCase
{
    private User $entity;

    private CamperData $data;

    private CamperCreateEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getCamperData());

        $newData = new CamperData(true);
        $this->event->setCamperData($newData);
        $this->assertSame($newData, $this->event->getCamperData());
    }

    public function testEntity(): void
    {
        $this->assertSame($this->entity, $this->event->getUser());

        $newEntity = new User('bob.new@gmail.com');
        $this->event->setUser($newEntity);
        $this->assertSame($newEntity, $this->event->getUser());
    }

    protected function setUp(): void
    {
        $this->entity = new User('bob@gmail.com');
        $this->data = new CamperData(true);
        $this->event = new CamperCreateEvent($this->data, $this->entity);
    }
}