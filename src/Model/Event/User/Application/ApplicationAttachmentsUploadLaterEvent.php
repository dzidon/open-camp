<?php

namespace App\Model\Event\User\Application;

use App\Library\Data\User\ApplicationAttachmentsUploadLaterData;
use App\Model\Entity\Application;
use App\Model\Event\AbstractModelEvent;

class ApplicationAttachmentsUploadLaterEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.application.attachments_upload_later';

    private ApplicationAttachmentsUploadLaterData $data;

    private Application $entity;

    public function __construct(ApplicationAttachmentsUploadLaterData $data, Application $entity)
    {
        $this->data = $data;
        $this->entity = $entity;
    }

    public function getApplicationAttachmentsUploadLaterData(): ApplicationAttachmentsUploadLaterData
    {
        return $this->data;
    }

    public function setApplicationAttachmentsUploadLaterData(ApplicationAttachmentsUploadLaterData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getApplication(): Application
    {
        return $this->entity;
    }

    public function setApplication(Application $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}