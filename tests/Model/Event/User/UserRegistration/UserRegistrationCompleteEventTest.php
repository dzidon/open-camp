<?php

namespace App\Tests\Model\Event\User\UserRegistration;

use App\Library\Data\User\PlainPasswordData;
use App\Model\Entity\UserRegistration;
use App\Model\Event\User\UserRegistration\UserRegistrationCompleteEvent;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class UserRegistrationCompleteEventTest extends TestCase
{
    private PlainPasswordData $data;

    private UserRegistration $entity;

    private UserRegistrationCompleteEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getPlainPasswordData());

        $newData = new PlainPasswordData();
        $this->event->setPlainPasswordData($newData);
        $this->assertSame($newData, $this->event->getPlainPasswordData());
    }

    public function testEntity(): void
    {
        $this->assertSame($this->entity, $this->event->getUserRegistration());

        $newEntity = new UserRegistration('bob@gmail.com', new DateTimeImmutable(), 'foo', '123');
        $this->event->setUserRegistration($newEntity);
        $this->assertSame($newEntity, $this->event->getUserRegistration());
    }

    protected function setUp(): void
    {
        $this->data = new PlainPasswordData();
        $this->entity = new UserRegistration('bob@gmail.com', new DateTimeImmutable(), 'foo', '123');
        $this->event = new UserRegistrationCompleteEvent($this->data, $this->entity);
    }
}