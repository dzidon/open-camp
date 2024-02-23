<?php

namespace App\Model\EventSubscriber\User\Application;

use App\Model\Event\User\Application\ApplicationAttachmentsUploadLaterEvent;
use App\Model\Repository\ApplicationRepositoryInterface;
use App\Model\Service\ApplicationAttachment\ApplicationAttachmentsUploadLaterInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationAttachmentsUploadLaterSubscriber
{
    private ApplicationRepositoryInterface $applicationRepository;

    private ApplicationAttachmentsUploadLaterInterface $applicationAttachmentsUploadLater;

    public function __construct(ApplicationRepositoryInterface             $applicationRepository,
                                ApplicationAttachmentsUploadLaterInterface $applicationAttachmentsUploadLater)
    {
        $this->applicationRepository = $applicationRepository;
        $this->applicationAttachmentsUploadLater = $applicationAttachmentsUploadLater;
    }

    #[AsEventListener(event: ApplicationAttachmentsUploadLaterEvent::NAME, priority: 200)]
    public function onUploadUpdateApplicationAttachments(ApplicationAttachmentsUploadLaterEvent $event): void
    {
        $applicationAttachmentsUploadLaterData = $event->getApplicationAttachmentsUploadLaterData();
        $this->applicationAttachmentsUploadLater->uploadApplicationAttachments($applicationAttachmentsUploadLaterData);
    }

    #[AsEventListener(event: ApplicationAttachmentsUploadLaterEvent::NAME, priority: 100)]
    public function onUploadSaveApplication(ApplicationAttachmentsUploadLaterEvent $event): void
    {
        $application = $event->getApplication();
        $isFlush = $event->isFlush();
        $this->applicationRepository->saveApplication($application, $isFlush);
    }
}