<?php

namespace App\Model\Event\User\Application;

use App\Library\Data\User\ApplicationStepThreeUpdateData;
use App\Model\Entity\Application;
use App\Model\Event\AbstractModelEvent;

class ApplicationStepThreeUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.application.step_three_update';

    private ApplicationStepThreeUpdateData $data;

    private Application $application;

    public function __construct(ApplicationStepThreeUpdateData $data, Application $application)
    {
        $this->data = $data;
        $this->application = $application;
    }

    public function getApplicationStepThreeUpdateData(): ApplicationStepThreeUpdateData
    {
        return $this->data;
    }

    public function setApplicationStepThreeUpdateData(ApplicationStepThreeUpdateData $data): self
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