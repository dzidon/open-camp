<?php

namespace App\Model\Event\User\ApplicationTripLocationPath;

use App\Library\Data\User\ApplicationCamperData;
use App\Model\Entity\ApplicationCamper;
use App\Model\Entity\ApplicationTripLocationPath;
use App\Model\Event\AbstractModelEvent;

class ApplicationTripLocationPathCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.application_trip_location_path.create';

    private bool $isThere;

    private ApplicationCamperData $data;

    private ApplicationCamper $applicationCamper;

    private ?ApplicationTripLocationPath $applicationTripLocationPath = null;

    public function __construct(bool $isThere, ApplicationCamperData $data, ApplicationCamper $applicationCamper)
    {
        $this->isThere = $isThere;
        $this->data = $data;
        $this->applicationCamper = $applicationCamper;
    }

    public function isThere(): bool
    {
        return $this->isThere;
    }

    public function setIsThere(bool $isThere): self
    {
        $this->isThere = $isThere;

        return $this;
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

    public function getApplicationTripLocationPath(): ?ApplicationTripLocationPath
    {
        return $this->applicationTripLocationPath;
    }

    public function setApplicationTripLocationPath(?ApplicationTripLocationPath $applicationTripLocationPath): self
    {
        $this->applicationTripLocationPath = $applicationTripLocationPath;

        return $this;
    }
}