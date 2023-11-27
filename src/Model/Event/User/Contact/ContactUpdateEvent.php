<?php

namespace App\Model\Event\User\Contact;

use App\Library\Data\User\ContactData;
use App\Model\Entity\Contact;
use Symfony\Contracts\EventDispatcher\Event;

class ContactUpdateEvent extends Event
{
    public const NAME = 'model.user.contact.update';

    private ContactData $data;

    private Contact $entity;

    public function __construct(ContactData $data, Contact $entity)
    {
        $this->data = $data;
        $this->entity = $entity;
    }

    public function getContactData(): ContactData
    {
        return $this->data;
    }

    public function setContactData(ContactData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getContact(): Contact
    {
        return $this->entity;
    }

    public function setContact(Contact $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}