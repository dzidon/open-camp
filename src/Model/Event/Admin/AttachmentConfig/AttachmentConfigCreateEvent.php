<?php

namespace App\Model\Event\Admin\AttachmentConfig;

use App\Library\Data\Admin\AttachmentConfigData;
use App\Model\Entity\AttachmentConfig;
use App\Model\Event\AbstractModelEvent;

class AttachmentConfigCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.attachment_config.create';

    private AttachmentConfigData $data;

    private ?AttachmentConfig $attachmentConfig = null;

    public function __construct(AttachmentConfigData $data)
    {
        $this->data = $data;
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

    public function getAttachmentConfig(): ?AttachmentConfig
    {
        return $this->attachmentConfig;
    }

    public function setAttachmentConfig(?AttachmentConfig $attachmentConfig): self
    {
        $this->attachmentConfig = $attachmentConfig;

        return $this;
    }
}