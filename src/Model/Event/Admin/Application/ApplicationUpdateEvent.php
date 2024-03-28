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

    private bool $isStateChange;

    public function __construct(ApplicationData $data, Application $entity)
    {
        $this->data = $data;
        $this->entity = $entity;

        if ($this->entity->isAccepted() !== $this->data->isAccepted())
        {
            $this->isStateChange = true;
        }
        else
        {
            $this->isStateChange = false;
        }
    }

    public function isStateChange(): bool
    {
        return $this->isStateChange;
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