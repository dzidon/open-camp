<?php

namespace App\Tests\Model\Event\User\Camper;

use App\Library\Enum\GenderEnum;
use App\Model\Entity\Camper;
use App\Model\Entity\User;
use App\Model\Event\User\Camper\CamperDeleteEvent;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class CamperDeleteEventTest extends TestCase
{
    private User $user;

    private Camper $entity;

    private CamperDeleteEvent $event;

    public function testEntity(): void
    {
        $this->assertSame($this->entity, $this->event->getCamper());

        $newEntity = new Camper('John', 'Doe', GenderEnum::MALE, new DateTimeImmutable(), $this->user);
        $this->event->setCamper($newEntity);
        $this->assertSame($newEntity, $this->event->getCamper());
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
        $this->entity = new Camper('Bob', 'Smith', GenderEnum::MALE, new DateTimeImmutable(), $this->user);
        $this->event = new CamperDeleteEvent($this->entity);
    }
}