<?php

namespace App\Model\Event\Admin\ApplicationAdminAttachment;

use App\Model\Entity\ApplicationAdminAttachment;
use App\Model\Event\AbstractModelEvent;

class ApplicationAdminAttachmentDeleteEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.application_admin_attachment.delete';

    private ApplicationAdminAttachment $entity;

    public function __construct(ApplicationAdminAttachment $entity)
    {
        $this->entity = $entity;
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