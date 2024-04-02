<?php

namespace App\Model\Event\Admin\ApplicationCamper;

use App\Library\Data\Admin\ApplicationCamperData;
use App\Model\Entity\ApplicationCamper;
use App\Model\Event\AbstractModelEvent;

class ApplicationCamperUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.application_camper.update';

    private ApplicationCamperData $data;

    private ApplicationCamper $entity;

    public function __construct(ApplicationCamperData $data, ApplicationCamper $entity)
    {
        $this->data = $data;
        $this->entity = $entity;
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