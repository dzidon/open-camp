<?php

namespace App\Model\Event\Admin\ApplicationContact;

use App\Library\Data\Common\ContactData;
use App\Model\Entity\ApplicationContact;
use App\Model\Event\AbstractModelEvent;

class ApplicationContactUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.application_contact.update';

    private ContactData $data;

    private ApplicationContact $entity;

    public function __construct(ContactData $data, ApplicationContact $entity)
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

    public function getApplicationContact(): ApplicationContact
    {
        return $this->entity;
    }

    public function setApplicationContact(ApplicationContact $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}