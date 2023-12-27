<?php

namespace App\Model\EventSubscriber\Admin\AttachmentConfig;

use App\Model\Entity\FileExtension;
use App\Model\Event\Admin\AttachmentConfig\AttachmentConfigFileExtensionCreateEvent;
use App\Model\Repository\FileExtensionRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class AttachmentConfigFileExtensionCreateSubscriber
{
    private FileExtensionRepositoryInterface $fileExtensionRepository;

    public function __construct(FileExtensionRepositoryInterface $fileExtensionRepository)
    {
        $this->fileExtensionRepository = $fileExtensionRepository;
    }

    #[AsEventListener(event: AttachmentConfigFileExtensionCreateEvent::NAME, priority: 200)]
    public function onCreateAddNewFileExtension(AttachmentConfigFileExtensionCreateEvent $event): void
    {
        $extension = $event->getExtension();
        $attachmentConfig = $event->getAttachmentConfig();
        $newFileExtension = new FileExtension($extension);
        $attachmentConfig->addFileExtension($newFileExtension);
        $event->setFileExtension($newFileExtension);
    }

    #[AsEventListener(event: AttachmentConfigFileExtensionCreateEvent::NAME, priority: 100)]
    public function onCreateSaveFileExtension(AttachmentConfigFileExtensionCreateEvent $event): void
    {
        $fileExtension = $event->getFileExtension();
        $flush = $event->isFlush();
        $this->fileExtensionRepository->saveFileExtension($fileExtension, $flush);
    }
}