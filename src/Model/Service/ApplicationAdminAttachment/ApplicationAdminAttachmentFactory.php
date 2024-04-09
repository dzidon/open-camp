<?php

namespace App\Model\Service\ApplicationAdminAttachment;

use App\Model\Entity\Application;
use App\Model\Entity\ApplicationAdminAttachment;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @inheritDoc
 */
class ApplicationAdminAttachmentFactory implements ApplicationAdminAttachmentFactoryInterface
{
    private FilesystemOperator $applicationAdminAttachmentStorage;

    public function __construct(FilesystemOperator $applicationAdminAttachmentStorage)
    {
        $this->applicationAdminAttachmentStorage = $applicationAdminAttachmentStorage;
    }

    /**
     * @inheritDoc
     */
    public function createApplicationAdminAttachment(File $file, Application $application, string $label): ApplicationAdminAttachment
    {
        $extension = $file->guessExtension();
        $applicationAdminAttachment = new ApplicationAdminAttachment($label, $extension, $application);
        $newFileName = $applicationAdminAttachment->getFileName();
        $contents = $file->getContent();

        $this->applicationAdminAttachmentStorage->write($newFileName, $contents);

        return $applicationAdminAttachment;
    }
}