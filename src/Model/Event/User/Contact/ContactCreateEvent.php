<?php

namespace App\Model\Event\User\Contact;

use App\Library\Data\Common\ContactData;
use App\Model\Entity\Contact;
use App\Model\Entity\User;
use App\Model\Event\AbstractModelEvent;

class ContactCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.contact.create';

    private ContactData $data;

    private User $user;

    private ?Contact $contact = null;

    public function __construct(ContactData $data, User $user)
    {
        $this->data = $data;
        $this->user = $user;
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

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): self
    {
        $this->contact = $contact;

        return $this;
    }
}