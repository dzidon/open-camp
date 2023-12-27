<?php

namespace App\Tests\Model\Event\Admin\User;

use App\Library\Data\Admin\UserData;
use App\Model\Entity\User;
use App\Model\Event\Admin\User\UserCreateEvent;
use PHPUnit\Framework\TestCase;

class UserCreateEventTest extends TestCase
{
    private UserData $data;

    private UserCreateEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getUserData());

        $newData = new UserData(true);
        $this->event->setUserData($newData);
        $this->assertSame($newData, $this->event->getUserData());
    }

    public function testEntity(): void
    {
        $this->assertNull($this->event->getUser());

        $newEntity = new User('bob.new@gmail.com');
        $this->event->setUser($newEntity);
        $this->assertSame($newEntity, $this->event->getUser());
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
        $this->data = new UserData(true);
        $this->event = new UserCreateEvent($this->data);
    }
}