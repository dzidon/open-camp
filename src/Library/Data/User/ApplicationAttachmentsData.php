<?php

namespace App\Library\Data\User;

use App\Model\Entity\Application;
use App\Model\Entity\ApplicationCamper;
use LogicException;
use Symfony\Component\Validator\Constraints as Assert;

class ApplicationAttachmentsData
{
    private ?Application $application;

    private ?ApplicationCamper $applicationCamper;

    /** @var ApplicationAttachmentData[] */
    #[Assert\Valid]
    private array $applicationAttachmentsData = [];

    public function __construct(?Application $application = null, ?ApplicationCamper $applicationCamper = null)
    {
        $this->application = $application;
        $this->applicationCamper = $applicationCamper;
        $this->assertApplicationAndApplicationCamper();
    }

    public function getApplication(): ?Application
    {
        return $this->application;
    }

    public function getApplicationCamper(): ?ApplicationCamper
    {
        return $this->applicationCamper;
    }

    public function getApplicationAttachmentsData(): array
    {
        return $this->applicationAttachmentsData;
    }

    public function addApplicationAttachmentsDatum(ApplicationAttachmentData $applicationAttachmentData): self
    {
        if (in_array($applicationAttachmentData, $this->applicationAttachmentsData, true))
        {
            return $this;
        }

        $this->applicationAttachmentsData[] = $applicationAttachmentData;

        return $this;
    }

    public function removeApplicationAttachmentsDatum(ApplicationAttachmentData $applicationAttachmentData): self
    {
        $key = array_search($applicationAttachmentData, $this->applicationAttachmentsData, true);

        if ($key === false)
        {
            return $this;
        }

        unset($this->applicationAttachmentsData[$key]);

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