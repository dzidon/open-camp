<?php

namespace App\Model\Service\ApplicationAttachment;

use App\Model\Entity\ApplicationAttachment;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @inheritDoc
 */
class ApplicationAttachmentFilesystem implements ApplicationAttachmentFilesystemInterface
{
    private FilesystemOperator $applicationAttachmentStorage;

    public function __construct(FilesystemOperator $applicationAttachmentStorage)
    {
        $this->applicationAttachmentStorage = $applicationAttachmentStorage;
    }

    /**
     * @inheritDoc
     */
    public function uploadFile(File $file, ApplicationAttachment $applicationAttachment): void
    {
        $this->removeFile($applicationAttachment);

        $extension = $file->guessExtension();
        $applicationAttachment->setExtension($extension);
        $idString = $applicationAttachment
            ->getId()
            ->toRfc4122()
        ;

        $newFileName = $idString . '.' . $extension;
        $contents = $file->getContent();
        $this->applicationAttachmentStorage->write($newFileName, $contents);
    }

    /**
     * @inheritDoc
     */
    public function removeFile(ApplicationAttachment $applicationAttachment): void
    {
        if ($applicationAttachment->getExtension() === null)
        {
            return;
        }

        $fileName = $this->getApplicationAttachmentName($applicationAttachment);
        $this->applicationAttachmentStorage->delete($fileName);
        $applicationAttachment->setExtension(null);
    }

    private function getApplicationAttachmentName(ApplicationAttachment $applicationAttachment): string
    {
        $applicationAttachmentId = $applicationAttachment->getId();

        return $applicationAttachmentId->toRfc4122() . '.' . $applicationAttachment->getExtension();
    }
}