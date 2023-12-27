<?php

namespace App\Model\Event\User\Application;

use App\Library\Data\User\ApplicationStepOneData;
use App\Model\Entity\Application;
use App\Model\Event\AbstractModelEvent;

class ApplicationStepOneUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.application.step_one_update';

    private ApplicationStepOneData $data;

    private Application $entity;

    public function __construct(ApplicationStepOneData $data, Application $entity)
    {
        $this->data = $data;
        $this->entity = $entity;
    }

    public function getApplicationStepOneData(): ApplicationStepOneData
    {
        return $this->data;
    }

    public function setApplicationStepOneData(ApplicationStepOneData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getApplication(): Application
    {
        return $this->entity;
    }

    public function setApplication(Application $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}