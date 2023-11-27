<?php

namespace App\Model\Event\Admin\AttachmentConfig;

use App\Model\Entity\AttachmentConfig;
use Symfony\Contracts\EventDispatcher\Event;

class AttachmentConfigDeleteEvent extends Event
{
    public const NAME = 'model.admin.attachment_config.delete';

    private AttachmentConfig $entity;

    public function __construct(AttachmentConfig $entity)
    {
        $this->entity = $entity;
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