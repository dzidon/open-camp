<?php

namespace App\Tests\Model\Event\Admin\User;

use App\Library\Data\Admin\PlainPasswordData;
use App\Model\Entity\User;
use App\Model\Event\Admin\User\UserUpdatePasswordEvent;
use PHPUnit\Framework\TestCase;

class UserUpdatePasswordEventTest extends TestCase
{
    private User $entity;

    private PlainPasswordData $data;

    private UserUpdatePasswordEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getPlainPasswordData());

        $newData = new PlainPasswordData();
        $this->event->setPlainPasswordData($newData);
        $this->assertSame($newData, $this->event->getPlainPasswordData());
    }

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
        $this->data = new PlainPasswordData();
        $this->event = new UserUpdatePasswordEvent($this->data, $this->entity);
    }
}