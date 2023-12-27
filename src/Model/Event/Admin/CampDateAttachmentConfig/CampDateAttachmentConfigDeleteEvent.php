<?php

namespace App\Model\Event\Admin\CampDateAttachmentConfig;

use App\Model\Entity\CampDateAttachmentConfig;
use App\Model\Event\AbstractModelEvent;

class CampDateAttachmentConfigDeleteEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.camp_date_attachment_config.delete';

    private CampDateAttachmentConfig $entity;

    public function __construct(CampDateAttachmentConfig $entity)
    {
        $this->entity = $entity;
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