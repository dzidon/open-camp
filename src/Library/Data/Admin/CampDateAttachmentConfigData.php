<?php

namespace App\Library\Data\Admin;

use App\Model\Entity\AttachmentConfig;
use Symfony\Component\Validator\Constraints as Assert;

class CampDateAttachmentConfigData
{
    #[Assert\NotBlank]
    private ?AttachmentConfig $attachmentConfig = null;

    #[Assert\NotBlank]
    private ?int $priority = 0;

    public function getAttachmentConfig(): ?AttachmentConfig
    {
        return $this->attachmentConfig;
    }

    public function setAttachmentConfig(?AttachmentConfig $attachmentConfig): self
    {
        $this->attachmentConfig = $attachmentConfig;

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(?int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }
}