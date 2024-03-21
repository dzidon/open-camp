<?php

namespace App\Model\Event\User\ApplicationContact;

use App\Library\Data\Common\ContactData;
use App\Model\Entity\Application;
use App\Model\Entity\ApplicationContact;
use App\Model\Event\AbstractModelEvent;

class ApplicationContactCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.application_contact.create';

    private ContactData $data;

    private Application $application;

    private ?ApplicationContact $applicationContact = null;

    private int $priority;

    public function __construct(ContactData $data, Application $application, int $priority)
    {
        $this->data = $data;
        $this->application = $application;
        $this->priority = $priority;
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

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }
}