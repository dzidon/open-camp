<?php

namespace App\Model\Event\Admin\Application;

use App\Model\Entity\Application;
use App\Model\Event\AbstractModelEvent;

class ApplicationDeleteEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.application.delete';

    private Application $entity;

    public function __construct(Application $entity)
    {
        $this->entity = $entity;
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