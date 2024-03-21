<?php

namespace App\Tests\Model\Event\User\Contact;

use App\Library\Data\Common\ContactData;
use App\Model\Entity\Contact;
use App\Model\Entity\User;
use App\Model\Enum\Entity\ContactRoleEnum;
use App\Model\Event\User\Contact\ContactCreateEvent;
use PHPUnit\Framework\TestCase;

class ContactCreateEventTest extends TestCase
{
    private User $user;

    private ContactData $data;

    private ContactCreateEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getContactData());

        $newData = new ContactData(false, false);
        $this->event->setContactData($newData);
        $this->assertSame($newData, $this->event->getContactData());
    }

    public function testUser(): void
    {
        $this->assertSame($this->user, $this->event->getUser());

        $newEntity = new User('bob.new@gmail.com');
        $this->event->setUser($newEntity);
        $this->assertSame($newEntity, $this->event->getUser());
    }

    public function testContact(): void
    {
        $this->assertNull($this->event->getContact());

        $newContact = new Contact('Sam', 'Doe', $this->user, ContactRoleEnum::MOTHER);
        $this->event->setContact($newContact);
        $this->assertSame($newContact, $this->event->getContact());
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
        $this->data = new ContactData(false, false);
        $this->event = new ContactCreateEvent($this->data, $this->user);
    }
}