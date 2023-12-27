<?php

namespace App\Model\Event\Admin\AttachmentConfig;

use App\Library\Data\Admin\AttachmentConfigData;
use App\Model\Entity\AttachmentConfig;
use App\Model\Event\AbstractModelEvent;

class AttachmentConfigUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.attachment_config.update';

    private AttachmentConfigData $data;

    private AttachmentConfig $entity;

    public function __construct(AttachmentConfigData $data, AttachmentConfig $entity)
    {
        $this->data = $data;
        $this->entity = $entity;
    }

    public function getAttachmentConfigData(): AttachmentConfigData
    {
        return $this->data;
    }

    public function setAttachmentConfigData(AttachmentConfigData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getAttachmentConfig(): AttachmentConfig
    {
        return $this->entity;
    }

    public function setAttachmentConfig(AttachmentConfig $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}