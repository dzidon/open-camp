<?php

namespace App\Model\EventSubscriber\User\ApplicationCamper;

use App\Model\Event\User\ApplicationCamper\ApplicationCamperDeleteEvent;
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

    #[AsEventListener(event: ApplicationCamperDeleteEvent::NAME, priority: 300)]
    public function onDeleteRemoveAttachments(ApplicationCamperDeleteEvent $event): void
    {
        $applicationCamper = $event->getApplicationCamper();
        $attachments = $applicationCamper->getApplicationAttachments();

        foreach ($attachments as $attachment)
        {
            $this->applicationAttachmentFilesystem->removeFile($attachment);
        }
    }

    #[AsEventListener(event: ApplicationCamperDeleteEvent::NAME, priority: 200)]
    public function onDeleteRemoveEntity(ApplicationCamperDeleteEvent $event): void
    {
        $applicationCamper = $event->getApplicationCamper();
        $flush = $event->isFlush();
        $this->repository->removeApplicationCamper($applicationCamper, $flush);
    }

    #[AsEventListener(event: ApplicationCamperDeleteEvent::NAME, priority: 100)]
    public function onDeleteRemoveFromCampDateCollection(ApplicationCamperDeleteEvent $event): void
    {
        $applicationCamper = $event->getApplicationCamper();
        $application = $applicationCamper->getApplication();
        $application->removeApplicationCamper($applicationCamper);
    }
}