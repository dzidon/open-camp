<?php

namespace App\Model\Event\User\ApplicationContact;

use App\Library\Data\User\ContactData;
use App\Model\Entity\Application;
use App\Model\Entity\ApplicationContact;
use App\Model\Event\AbstractModelEvent;

class ApplicationContactCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.application_contact.create';

    private ContactData $data;

    private Application $application;

    private ?ApplicationContact $applicationContact = null;

    public function __construct(ContactData $data, Application $application)
    {
        $this->data = $data;
        $this->application = $application;
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

    public function getApplication(): Application
    {
        return $this->application;
    }

    public function setApplication(Application $application): self
    {
        $this->application = $application;

        return $this;
    }

    public function getApplicationContact(): ?ApplicationContact
    {
        return $this->applicationContact;
    }

    public function setApplicationContact(?ApplicationContact $applicationContact): self
    {
        $this->applicationContact = $applicationContact;

        return $this;
    }
}