<?php

namespace App\Model\Event\User\ApplicationCamper;

use App\Library\Data\User\ApplicationCamperData;
use App\Model\Entity\Application;
use App\Model\Entity\ApplicationCamper;
use App\Model\Event\AbstractModelEvent;

class ApplicationCamperCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.application_camper.create';

    private ApplicationCamperData $data;

    private Application $application;

    private ?ApplicationCamper $applicationCamper = null;

    public function __construct(ApplicationCamperData $data, Application $application)
    {
        $this->data = $data;
        $this->application = $application;
    }

    public function getApplicationCamperData(): ApplicationCamperData
    {
        return $this->data;
    }

    public function setApplicationCamperData(ApplicationCamperData $data): self
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

    public function getApplicationCamper(): ?ApplicationCamper
    {
        return $this->applicationCamper;
    }

    public function setApplicationCamper(?ApplicationCamper $applicationCamper): self
    {
        $this->applicationCamper = $applicationCamper;

        return $this;
    }
}