<?php

namespace App\Model\Service\ApplicationAdminAttachment;

use App\Model\Entity\ApplicationAdminAttachment;
use League\Flysystem\FilesystemOperator;

/**
 * @inheritDoc
 */
class ApplicationAdminAttachmentFilesystem implements ApplicationAdminAttachmentFilesystemInterface
{
    private FilesystemOperator $applicationAdminAttachmentStorage;

    public function __construct(FilesystemOperator $applicationAdminAttachmentStorage)
    {
        $this->applicationAdminAttachmentStorage = $applicationAdminAttachmentStorage;
    }

    /**
     * @inheritDoc
     */
    public function getFileContents(ApplicationAdminAttachment $applicationAdminAttachment): ?string
    {
        $fileName = $applicationAdminAttachment->getFileName();

        if (!$this->applicationAdminAttachmentStorage->has($fileName))
        {
            return null;
        }

        return $this->applicationAdminAttachmentStorage->read($fileName);
    }

    /**
     * @inheritDoc
     */
    public function removeFile(ApplicationAdminAttachment $applicationAdminAttachment): void
    {
        $fileName = $applicationAdminAttachment->getFileName();
        $this->applicationAdminAttachmentStorage->delete($fileName);
    }
}