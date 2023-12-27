<?php

namespace App\Model\Event\Admin\CampDateAttachmentConfig;

use App\Library\Data\Admin\CampDateAttachmentConfigData;
use App\Model\Entity\CampDateAttachmentConfig;
use App\Model\Event\AbstractModelEvent;

class CampDateAttachmentConfigUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.camp_date_attachment_config.update';

    private CampDateAttachmentConfigData $data;

    private CampDateAttachmentConfig $entity;

    public function __construct(CampDateAttachmentConfigData $data, CampDateAttachmentConfig $entity)
    {
        $this->data = $data;
        $this->entity = $entity;
    }

    public function getCampDateAttachmentConfigData(): CampDateAttachmentConfigData
    {
        return $this->data;
    }

    public function setCampDateAttachmentConfigData(CampDateAttachmentConfigData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getCampDateAttachmentConfig(): CampDateAttachmentConfig
    {
        return $this->entity;
    }

    public function setCampDateAttachmentConfig(CampDateAttachmentConfig $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}