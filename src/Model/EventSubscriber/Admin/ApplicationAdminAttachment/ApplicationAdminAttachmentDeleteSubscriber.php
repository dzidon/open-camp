<?php

namespace App\Model\EventSubscriber\Admin\ApplicationAdminAttachment;

use App\Model\Event\Admin\ApplicationAdminAttachment\ApplicationAdminAttachmentDeleteEvent;
use App\Model\Repository\ApplicationAdminAttachmentRepositoryInterface;
use App\Model\Service\ApplicationAdminAttachment\ApplicationAdminAttachmentFilesystemInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationAdminAttachmentDeleteSubscriber
{
    private ApplicationAdminAttachmentFilesystemInterface $applicationAdminAttachmentFilesystem;

    private ApplicationAdminAttachmentRepositoryInterface $applicationAdminAttachmentRepository;

    public function __construct(ApplicationAdminAttachmentFilesystemInterface $applicationAdminAttachmentFilesystem,
                                ApplicationAdminAttachmentRepositoryInterface $applicationAdminAttachmentRepository)
    {
        $this->applicationAdminAttachmentFilesystem = $applicationAdminAttachmentFilesystem;
        $this->applicationAdminAttachmentRepository = $applicationAdminAttachmentRepository;
    }

    #[AsEventListener(event: ApplicationAdminAttachmentDeleteEvent::NAME, priority: 300)]
    public function onDeleteRemoveFile(ApplicationAdminAttachmentDeleteEvent $event): void
    {
        $entity = $event->getApplicationAdminAttachment();
        $this->applicationAdminAttachmentFilesystem->removeFile($entity);
    }

    #[AsEventListener(event: ApplicationAdminAttachmentDeleteEvent::NAME, priority: 200)]
    public function onDeleteRemoveEntity(ApplicationAdminAttachmentDeleteEvent $event): void
    {
        $entity = $event->getApplicationAdminAttachment();
        $isFlush = $event->isFlush();
        $this->applicationAdminAttachmentRepository->removeApplicationAdminAttachment($entity, $isFlush);
    }

    #[AsEventListener(event: ApplicationAdminAttachmentDeleteEvent::NAME, priority: 100)]
    public function onDeleteRemoveFromApplicationCollection(ApplicationAdminAttachmentDeleteEvent $event): void
    {
        $entity = $event->getApplicationAdminAttachment();
        $application = $entity->getApplication();
        $application->removeApplicationAdminAttachment($entity);
    }
}