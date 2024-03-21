<?php

namespace App\Model\Event\User\ApplicationCamper;

use App\Library\Data\Common\ApplicationCamperData;
use App\Model\Entity\ApplicationCamper;
use App\Model\Event\AbstractModelEvent;

class ApplicationCamperUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.application_camper.update';

    private ApplicationCamperData $data;

    private ApplicationCamper $applicationCamper;

    public function __construct(ApplicationCamperData $data, ApplicationCamper $applicationCamper)
    {
        $this->data = $data;
        $this->applicationCamper = $applicationCamper;
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

    public function getApplicationCamper(): ApplicationCamper
    {
        return $this->applicationCamper;
    }

    public function setApplicationCamper(ApplicationCamper $applicationCamper): self
    {
        $this->applicationCamper = $applicationCamper;

        return $this;
    }
}