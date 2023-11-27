<?php

namespace App\Tests\Model\Event\User\Contact;

use App\Library\Data\User\ContactData;
use App\Model\Entity\Contact;
use App\Model\Entity\User;
use App\Model\Enum\Entity\ContactRoleEnum;
use App\Model\Event\User\Contact\ContactUpdateEvent;
use PHPUnit\Framework\TestCase;

class ContactUpdateEventTest extends TestCase
{
    private Contact $entity;

    private ContactData $data;

    private ContactUpdateEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getContactData());

        $newData = new ContactData();
        $this->event->setContactData($newData);
        $this->assertSame($newData, $this->event->getContactData());
    }

    public function testEntity(): void
    {
        $this->assertSame($this->entity, $this->event->getContact());

        $newContact = new Contact('Sam', 'Doe', $this->user, ContactRoleEnum::MOTHER);
        $this->event->setContact($newContact);
        $this->assertSame($newContact, $this->event->getContact());
    }

    protected function setUp(): void
    {
        $this->user = new User('bob@gmail.com');
        $this->entity = new Contact('Bob', 'Smith', $this->user, ContactRoleEnum::MOTHER);
        $this->data = new ContactData();
        $this->event = new ContactUpdateEvent($this->data, $this->entity);
    }
}