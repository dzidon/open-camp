<?php

namespace App\Tests\Model\Event\User\Contact;

use App\Library\Data\User\ContactData;
use App\Model\Entity\User;
use App\Model\Event\User\Contact\ContactCreateEvent;
use PHPUnit\Framework\TestCase;

class ContactCreateEventTest extends TestCase
{
    private User $entity;

    private ContactData $data;

    private ContactCreateEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getContactData());

        $newData = new ContactData();
        $this->event->setContactData($newData);
        $this->assertSame($newData, $this->event->getContactData());
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
        $this->data = new ContactData();
        $this->event = new ContactCreateEvent($this->data, $this->entity);
    }
}