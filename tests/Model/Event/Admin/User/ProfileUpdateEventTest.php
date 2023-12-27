<?php

namespace App\Tests\Model\Event\Admin\User;

use App\Library\Data\Admin\ProfileData;
use App\Model\Entity\User;
use App\Model\Event\Admin\User\ProfileUpdateEvent;
use PHPUnit\Framework\TestCase;

class ProfileUpdateEventTest extends TestCase
{
    private User $entity;

    private ProfileData $data;

    private ProfileUpdateEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getProfileData());

        $newData = new ProfileData();
        $this->event->setProfileData($newData);
        $this->assertSame($newData, $this->event->getProfileData());
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
        $this->data = new ProfileData();
        $this->event = new ProfileUpdateEvent($this->data, $this->entity);
    }
}