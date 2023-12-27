<?php

namespace App\Model\Event\User\Contact;

use App\Model\Entity\Contact;
use App\Model\Event\AbstractModelEvent;

class ContactDeleteEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.contact.delete';

    private Contact $entity;

    public function __construct(Contact $entity)
    {
        $this->entity = $entity;
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