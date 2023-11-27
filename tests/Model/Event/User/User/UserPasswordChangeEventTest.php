<?php

namespace App\Tests\Model\Event\User\User;

use App\Library\Data\User\ProfilePasswordChangeData;
use App\Model\Entity\User;
use App\Model\Event\User\User\UserPasswordChangeEvent;
use PHPUnit\Framework\TestCase;

class UserPasswordChangeEventTest extends TestCase
{
    private User $entity;

    private ProfilePasswordChangeData $data;

    private UserPasswordChangeEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getProfilePasswordChangeData());

        $newData = new ProfilePasswordChangeData();
        $this->event->setProfilePasswordChangeData($newData);
        $this->assertSame($newData, $this->event->getProfilePasswordChangeData());
    }

    public function testEntity(): void
    {
        $this->assertSame($this->entity, $this->event->getUser());

        $newUser = new User('bob.new@gmail.com');
        $this->event->setUser($newUser);
        $this->assertSame($newUser, $this->event->getUser());
    }

    protected function setUp(): void
    {
        $this->entity = new User('bob@gmail.com');
        $this->data = new ProfilePasswordChangeData();
        $this->event = new UserPasswordChangeEvent($this->data, $this->entity);
    }
}