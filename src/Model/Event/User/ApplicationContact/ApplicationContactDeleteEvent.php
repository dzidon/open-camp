<?php

namespace App\Model\Event\User\ApplicationContact;

use App\Model\Entity\ApplicationContact;
use App\Model\Event\AbstractModelEvent;

class ApplicationContactDeleteEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.application_contact.delete';

    private ApplicationContact $entity;

    public function __construct(ApplicationContact $entity)
    {
        $this->entity = $entity;
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