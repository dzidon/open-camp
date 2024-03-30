<?php

namespace App\Model\Event\User\Application;

use App\Library\Data\User\ApplicationStepThreeData;
use App\Model\Entity\Application;
use App\Model\Event\AbstractModelEvent;

class ApplicationStepThreeUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.application.step_three_update';

    private ApplicationStepThreeData $data;

    private Application $application;

    public function __construct(ApplicationStepThreeData $data, Application $application)
    {
        $this->data = $data;
        $this->application = $application;
    }

    public function getApplicationStepThreeUpdateData(): ApplicationStepThreeData
    {
        return $this->data;
    }

    public function setApplicationStepThreeUpdateData(ApplicationStepThreeData $data): self
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