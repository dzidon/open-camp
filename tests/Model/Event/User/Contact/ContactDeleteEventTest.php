<?php

namespace App\Tests\Model\Event\User\Contact;

use App\Model\Entity\Contact;
use App\Model\Entity\User;
use App\Model\Enum\Entity\ContactRoleEnum;
use App\Model\Event\User\Contact\ContactDeleteEvent;
use PHPUnit\Framework\TestCase;

class ContactDeleteEventTest extends TestCase
{
    private User $user;

    private Contact $entity;

    private ContactDeleteEvent $event;

    public function testEntity(): void
    {
        $this->assertSame($this->entity, $this->event->getContact());

        $newEntity = new Contact('John', 'Doe', $this->user, ContactRoleEnum::MOTHER);
        $this->event->setContact($newEntity);
        $this->assertSame($newEntity, $this->event->getContact());
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
        $this->entity = new Contact('Bob', 'Smith', $this->user, ContactRoleEnum::MOTHER);
        $this->event = new ContactDeleteEvent($this->entity);
    }
}