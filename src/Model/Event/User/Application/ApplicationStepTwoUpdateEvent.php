<?php

namespace App\Model\Event\User\Application;

use App\Library\Data\User\ApplicationStepTwoUpdateData;
use App\Model\Entity\Application;
use App\Model\Event\AbstractModelEvent;

class ApplicationStepTwoUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.application.step_two_update';

    private ApplicationStepTwoUpdateData $data;

    private Application $application;

    public function __construct(ApplicationStepTwoUpdateData $data, Application $application)
    {
        $this->data = $data;
        $this->application = $application;
    }

    public function getApplicationPurchasableItemsData(): ApplicationStepTwoUpdateData
    {
        return $this->data;
    }

    public function setApplicationPurchasableItemsData(ApplicationStepTwoUpdateData $data): self
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