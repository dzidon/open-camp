<?php

namespace App\Model\Event\Admin\ApplicationCamper;

use App\Model\Entity\ApplicationCamper;
use App\Model\Event\AbstractModelEvent;

class ApplicationCamperDeleteEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.application_camper.delete';

    private ApplicationCamper $entity;

    public function __construct(ApplicationCamper $entity)
    {
        $this->entity = $entity;
    }

    public function getApplicationCamper(): ApplicationCamper
    {
        return $this->entity;
    }

    public function setApplicationCamper(ApplicationCamper $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}