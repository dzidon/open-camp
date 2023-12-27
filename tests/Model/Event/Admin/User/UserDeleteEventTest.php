<?php

namespace App\Tests\Model\Event\Admin\User;

use App\Model\Entity\User;
use App\Model\Event\Admin\User\UserDeleteEvent;
use PHPUnit\Framework\TestCase;

class UserDeleteEventTest extends TestCase
{
    private User $entity;

    private UserDeleteEvent $event;

    public function testEntity(): void
    {
        $this->assertSame($this->entity, $this->event->getUser());

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
        $this->entity = new User('bob@gmail.com');
        $this->event = new UserDeleteEvent($this->entity);
    }
}