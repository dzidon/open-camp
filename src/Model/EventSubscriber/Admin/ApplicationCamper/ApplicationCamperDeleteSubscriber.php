<?php

namespace App\Model\EventSubscriber\Admin\ApplicationCamper;

use App\Model\Event\Admin\ApplicationCamper\ApplicationCamperDeleteEvent;
use App\Model\Repository\ApplicationCamperRepositoryInterface;
use App\Model\Service\ApplicationAttachment\ApplicationAttachmentFilesystemInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationCamperDeleteSubscriber
{
    private ApplicationCamperRepositoryInterface $repository;

    private ApplicationAttachmentFilesystemInterface $applicationAttachmentFilesystem;

    public function __construct(ApplicationCamperRepositoryInterface     $repository,
                                ApplicationAttachmentFilesystemInterface $applicationAttachmentFilesystem)
    {
        $this->repository = $repository;
        $this->applicationAttachmentFilesystem = $applicationAttachmentFilesystem;
    }

    #[AsEventListener(event: ApplicationCamperDeleteEvent::NAME, priority: 700)]
    public function onDeleteRemoveAttachments(ApplicationCamperDeleteEvent $event): void
    {
        $applicationCamper = $event->getApplicationCamper();
        $attachments = $applicationCamper->getApplicationAttachments();

        foreach ($attachments as $attachment)
        {
            $this->applicationAttachmentFilesystem->removeFile($attachment);
        }
    }

    #[AsEventListener(event: ApplicationCamperDeleteEvent::NAME, priority: 300)]
    public function onDeleteRemoveFromApplicationCollection(ApplicationCamperDeleteEvent $event): void
    {
        $entity = $event->getApplicationCamper();
        $application = $entity->getApplication();
        $application->removeApplicationCamper($entity);
    }

    #[AsEventListener(event: ApplicationCamperDeleteEvent::NAME, priority: 100)]
    public function onDeleteRemoveEntity(ApplicationCamperDeleteEvent $event): void
    {
        $entity = $event->getApplicationCamper();
        $isFlush = $event->isFlush();
        $this->repository->removeApplicationCamper($entity, $isFlush);
    }
}