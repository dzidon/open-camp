<?php

namespace App\Model\Service\ApplicationAttachment;

use App\Model\Entity\ApplicationAttachment;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @inheritDoc
 */
class ApplicationAttachmentFilesystem implements ApplicationAttachmentFilesystemInterface
{
    private string $applicationAttachmentDirectory;
    private string $kernelProjectDirectory;

    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem,
                                string     $applicationAttachmentDirectory,
                                string     $kernelProjectDirectory)
    {
        $this->filesystem = $filesystem;

        $this->applicationAttachmentDirectory = $applicationAttachmentDirectory;
        $this->kernelProjectDirectory = $kernelProjectDirectory;
    }

    /**
     * @inheritDoc
     */
    public function getFilePath(ApplicationAttachment $applicationAttachment): ?string
    {
        if ($applicationAttachment->getExtension() === null)
        {
            return null;
        }

        $id = $applicationAttachment->getId();

        return $this->applicationAttachmentDirectory . '/' . $id->toRfc4122() . '.' . $applicationAttachment->getExtension();
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
        $file->move($this->applicationAttachmentDirectory, $newFileName);
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

        $filePath = $this->kernelProjectDirectory . '/public/' . $this->getFilePath($applicationAttachment);
        $this->filesystem->remove($filePath);
        $applicationAttachment->setExtension(null);
    }
}