<?php

namespace App\Model\Event\User\ApplicationContact;

use App\Library\Data\User\ContactData;
use App\Model\Entity\ApplicationContact;
use App\Model\Event\AbstractModelEvent;

class ApplicationContactUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.application_contact.update';

    private ContactData $data;

    private ApplicationContact $applicationContact;

    public function __construct(ContactData $data, ApplicationContact $applicationContact)
    {
        $this->data = $data;
        $this->applicationContact = $applicationContact;
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

    public function getApplicationContact(): ApplicationContact
    {
        return $this->applicationContact;
    }

    public function setApplicationContact(ApplicationContact $applicationContact): self
    {
        $this->applicationContact = $applicationContact;

        return $this;
    }
}