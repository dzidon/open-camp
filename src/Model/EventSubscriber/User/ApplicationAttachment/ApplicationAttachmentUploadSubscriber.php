<?php

namespace App\Model\EventSubscriber\User\ApplicationAttachment;

use App\Model\Event\User\ApplicationAttachment\ApplicationAttachmentCreateEvent;
use App\Model\Event\User\ApplicationAttachment\ApplicationAttachmentUpdateEvent;
use App\Model\Service\ApplicationAttachment\ApplicationAttachmentFilesystemInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationAttachmentUploadSubscriber
{
    private ApplicationAttachmentFilesystemInterface $applicationAttachmentFilesystem;

    public function __construct(ApplicationAttachmentFilesystemInterface $applicationAttachmentFilesystem)
    {
        $this->applicationAttachmentFilesystem = $applicationAttachmentFilesystem;
    }

    #[AsEventListener(event: ApplicationAttachmentCreateEvent::NAME, priority: 200)]
    #[AsEventListener(event: ApplicationAttachmentUpdateEvent::NAME, priority: 200)]
    public function onCreateOrUpdateUploadFile(ApplicationAttachmentCreateEvent|ApplicationAttachmentUpdateEvent $event): void
    {
        $data = $event->getApplicationAttachmentData();
        $entity = $event->getApplicationAttachment();
        $file = $data->getFile();

        if ($file === null)
        {
            return;
        }

        $this->applicationAttachmentFilesystem->uploadFile($file, $entity);
    }
}