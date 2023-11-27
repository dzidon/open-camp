<?php

namespace App\Model\Event\User\Contact;

use App\Library\Data\User\ContactData;
use App\Model\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class ContactCreateEvent extends Event
{
    public const NAME = 'model.user.contact.create';

    private ContactData $data;

    private User $user;

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
}