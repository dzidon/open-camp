<?php

namespace App\Model\Event\User\Application;

use App\Library\Data\User\ApplicationStepTwoData;
use App\Model\Entity\Application;
use App\Model\Event\AbstractModelEvent;

class ApplicationStepTwoUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.application.step_two_update';

    private ApplicationStepTwoData $data;

    private Application $application;

    public function __construct(ApplicationStepTwoData $data, Application $application)
    {
        $this->data = $data;
        $this->application = $application;
    }

    public function getApplicationStepTwoUpdateData(): ApplicationStepTwoData
    {
        return $this->data;
    }

    public function setApplicationStepTwoUpdateData(ApplicationStepTwoData $data): self
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
}