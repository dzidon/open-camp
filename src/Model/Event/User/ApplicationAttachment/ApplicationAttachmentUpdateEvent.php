<?php

namespace App\Model\Event\User\ApplicationAttachment;

use App\Library\Data\User\ApplicationAttachmentData;
use App\Model\Entity\ApplicationAttachment;
use App\Model\Event\AbstractModelEvent;

class ApplicationAttachmentUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.application_attachment.update';

    private ApplicationAttachmentData $data;

    private ApplicationAttachment $applicationAttachment;

    public function __construct(ApplicationAttachmentData $data, ApplicationAttachment $applicationAttachment)
    {
        $this->data = $data;
        $this->applicationAttachment = $applicationAttachment;
    }

    public function getApplicationAttachmentData(): ApplicationAttachmentData
    {
        return $this->data;
    }

    public function setApplicationAttachmentData(ApplicationAttachmentData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getApplicationAttachment(): ApplicationAttachment
    {
        return $this->applicationAttachment;
    }

    public function setApplicationAttachment(ApplicationAttachment $applicationAttachment): self
    {
        $this->applicationAttachment = $applicationAttachment;

        return $this;
    }
}