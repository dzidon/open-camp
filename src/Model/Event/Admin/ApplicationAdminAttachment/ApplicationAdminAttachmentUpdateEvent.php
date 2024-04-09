<?php

namespace App\Model\Event\Admin\ApplicationAdminAttachment;

use App\Library\Data\Admin\ApplicationAdminAttachmentUpdateData;
use App\Model\Entity\ApplicationAdminAttachment;
use App\Model\Event\AbstractModelEvent;

class ApplicationAdminAttachmentUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.application_admin_attachment.update';

    private ApplicationAdminAttachmentUpdateData $data;

    private ApplicationAdminAttachment $entity;

    public function __construct(ApplicationAdminAttachmentUpdateData $data, ApplicationAdminAttachment $entity)
    {
        $this->data = $data;
        $this->entity = $entity;
    }

    public function getApplicationAdminAttachmentUpdateData(): ApplicationAdminAttachmentUpdateData
    {
        return $this->data;
    }

    public function setApplicationAdminAttachmentUpdateData(ApplicationAdminAttachmentUpdateData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getApplicationAdminAttachment(): ApplicationAdminAttachment
    {
        return $this->entity;
    }

    public function setApplicationAdminAttachment(ApplicationAdminAttachment $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}