<?php

namespace App\Model\Event\Admin\ApplicationAdminAttachment;

use App\Library\Data\Admin\ApplicationAdminAttachmentCreateData;
use App\Model\Entity\Application;
use App\Model\Entity\ApplicationAdminAttachment;
use App\Model\Event\AbstractModelEvent;

class ApplicationAdminAttachmentCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.application_admin_attachment.create';

    private ApplicationAdminAttachmentCreateData $data;

    private Application $application;

    private ?ApplicationAdminAttachment $applicationAdminAttachment = null;

    public function __construct(ApplicationAdminAttachmentCreateData $data, Application $application)
    {
        $this->data = $data;
        $this->application = $application;
    }

    public function getApplicationAdminAttachmentCreateData(): ApplicationAdminAttachmentCreateData
    {
        return $this->data;
    }

    public function setApplicationAdminAttachmentCreateData(ApplicationAdminAttachmentCreateData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getApplication(): Application
    {
        return $this->application;
    }

    public function setApplication(Application $application): self
    {
        $this->application = $application;

        return $this;
    }

    public function getApplicationAdminAttachment(): ?ApplicationAdminAttachment
    {
        return $this->applicationAdminAttachment;
    }

    public function setApplicationAdminAttachment(?ApplicationAdminAttachment $applicationAdminAttachment): self
    {
        $this->applicationAdminAttachment = $applicationAdminAttachment;

        return $this;
    }
}