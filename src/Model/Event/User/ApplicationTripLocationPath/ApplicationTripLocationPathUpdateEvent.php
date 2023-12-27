<?php

namespace App\Model\Event\User\ApplicationTripLocationPath;

use App\Library\Data\User\ApplicationCamperData;
use App\Model\Entity\ApplicationTripLocationPath;
use App\Model\Event\AbstractModelEvent;

class ApplicationTripLocationPathUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.application_trip_location_path.update';

    private ApplicationCamperData $data;

    private ApplicationTripLocationPath $applicationTripLocationPath;

    public function __construct(ApplicationCamperData $data, ApplicationTripLocationPath $applicationTripLocationPath)
    {
        $this->data = $data;
        $this->applicationTripLocationPath = $applicationTripLocationPath;
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

    public function getApplicationTripLocationPath(): ApplicationTripLocationPath
    {
        return $this->applicationTripLocationPath;
    }

    public function setApplicationTripLocationPath(ApplicationTripLocationPath $applicationTripLocationPath): self
    {
        $this->applicationTripLocationPath = $applicationTripLocationPath;

        return $this;
    }
}