<?php

namespace App\Model\Event\Admin\AttachmentConfig;

use App\Model\Entity\AttachmentConfig;
use App\Model\Entity\FileExtension;
use App\Model\Event\AbstractModelEvent;

class AttachmentConfigFileExtensionCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.attachment_config_file_extension.create';

    private AttachmentConfig $attachmentConfig;

    private string $extension;

    private ?FileExtension $fileExtension = null;

    public function __construct(AttachmentConfig $attachmentConfig, string $extension)
    {
        $this->attachmentConfig = $attachmentConfig;
        $this->extension = $extension;
    }

    public function getAttachmentConfig(): AttachmentConfig
    {
        return $this->attachmentConfig;
    }

    public function setAttachmentConfig(AttachmentConfig $attachmentConfig): self
    {
        $this->attachmentConfig = $attachmentConfig;

        return $this;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    public function getFileExtension(): ?FileExtension
    {
        return $this->fileExtension;
    }

    public function setFileExtension(?FileExtension $fileExtension): self
    {
        $this->fileExtension = $fileExtension;

        return $this;
    }
}