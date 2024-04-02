<?php

namespace App\Model\Event\Admin\ApplicationAttachment;

use App\Library\Data\Common\ApplicationAttachmentData;
use App\Model\Entity\Application;
use App\Model\Entity\ApplicationAttachment;
use App\Model\Entity\ApplicationCamper;
use App\Model\Event\AbstractModelEvent;
use LogicException;

class ApplicationAttachmentCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.application_attachment.create';

    private ApplicationAttachmentData $data;

    private ?Application $application;

    private ?ApplicationCamper $applicationCamper;

    private ?ApplicationAttachment $applicationAttachment = null;

    public function __construct(ApplicationAttachmentData $data, ?Application $application, ?ApplicationCamper $applicationCamper)
    {
        $this->data = $data;
        $this->setApplicationAndApplicationCamper($application, $applicationCamper);
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

    public function getApplication(): ?Application
    {
        return $this->application;
    }

    public function getApplicationCamper(): ?ApplicationCamper
    {
        return $this->applicationCamper;
    }

    public function setApplicationAndApplicationCamper(?Application $application, ?ApplicationCamper $applicationCamper): self
    {
        $this->application = $application;
        $this->applicationCamper = $applicationCamper;
        $this->assertApplicationAndApplicationCamper();

        return $this;
    }

    public function getApplicationAttachment(): ?ApplicationAttachment
    {
        return $this->applicationAttachment;
    }

    public function setApplicationAttachment(?ApplicationAttachment $applicationAttachment): self
    {
        $this->applicationAttachment = $applicationAttachment;

        return $this;
    }

    private function assertApplicationAndApplicationCamper(): void
    {
        if ($this->application === null && $this->applicationCamper === null)
        {
            throw new LogicException(
                sprintf('%s cannot have $application and $applicationCamper both set to null.', self::class)
            );
        }

        if ($this->application !== null && $this->applicationCamper !== null)
        {
            throw new LogicException(
                sprintf('%s cannot have $application and $applicationCamper both set to not null values.', self::class)
            );
        }
    }
}