<?php

namespace App\Library\Data\User;

use App\Library\Constraint\ApplicationAttachmentsUploadLaterNotBlank;
use Symfony\Component\Validator\Constraints as Assert;

#[ApplicationAttachmentsUploadLaterNotBlank]
class ApplicationAttachmentsUploadLaterData
{
    /** @var ApplicationAttachmentsData[] */
    #[Assert\Valid]
    private array $applicationAttachmentsData = [];

    public function getApplicationAttachmentsData(): array
    {
        return $this->applicationAttachmentsData;
    }

    public function addApplicationAttachmentsDatum(ApplicationAttachmentsData $applicationAttachmentsData): self
    {
        if (in_array($applicationAttachmentsData, $this->applicationAttachmentsData, true))
        {
            return $this;
        }

        $this->applicationAttachmentsData[] = $applicationAttachmentsData;

        return $this;
    }

    public function removeApplicationAttachmentsDatum(ApplicationAttachmentsData $applicationAttachmentsData): self
    {
        $key = array_search($applicationAttachmentsData, $this->applicationAttachmentsData, true);

        if ($key === false)
        {
            return $this;
        }

        unset($this->applicationAttachmentsData[$key]);

        return $this;
    }
}