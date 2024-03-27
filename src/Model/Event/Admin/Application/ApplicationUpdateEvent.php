<?php

namespace App\Model\Event\Admin\Application;

use App\Library\Data\Admin\ApplicationData;
use App\Model\Entity\Application;
use App\Model\Event\AbstractModelEvent;

class ApplicationUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.application.update';

    private ApplicationData $data;

    private Application $entity;

    public function __construct(ApplicationData $data, Application $entity)
    {
        $this->data = $data;
        $this->entity = $entity;
    }

    public function getApplicationData(): ApplicationData
    {
        return $this->data;
    }

    public function setApplicationData(ApplicationData $data): self
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