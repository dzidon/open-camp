<?php

namespace App\Tests\Model\Event\Admin\User;

use App\Library\Data\Admin\UserData;
use App\Model\Entity\User;
use App\Model\Event\Admin\User\UserCreatedEvent;
use PHPUnit\Framework\TestCase;

class UserCreatedEventTest extends TestCase
{
    private User $entity;

    private UserData $data;

    private UserCreatedEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getUserData());

        $newData = new UserData(true);
        $this->event->setUserData($newData);
        $this->assertSame($newData, $this->event->getUserData());
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
        $this->data = new UserData(true);
        $this->event = new UserCreatedEvent($this->data, $this->entity);
    }
}