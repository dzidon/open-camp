<?php

namespace App\Model\Event\Admin\AttachmentConfig;

use App\Library\Data\Admin\AttachmentConfigData;
use Symfony\Contracts\EventDispatcher\Event;

class AttachmentConfigCreateEvent extends Event
{
    public const NAME = 'model.admin.attachment_config.create';

    private AttachmentConfigData $data;

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
}