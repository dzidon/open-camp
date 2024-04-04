<?php

namespace App\Model\EventSubscriber\User\Application;

use App\Model\Event\User\Application\ApplicationAttachmentsUploadLaterEvent;
use App\Model\Event\User\ApplicationAttachment\ApplicationAttachmentUpdateEvent;
use App\Model\Repository\ApplicationRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ApplicationAttachmentsUploadLaterSubscriber
{
    private ApplicationRepositoryInterface $applicationRepository;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(ApplicationRepositoryInterface $applicationRepository,
                                EventDispatcherInterface       $eventDispatcher)
    {
        $this->applicationRepository = $applicationRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    #[AsEventListener(event: ApplicationAttachmentsUploadLaterEvent::NAME, priority: 200)]
    public function onUploadUpdateApplicationAttachments(ApplicationAttachmentsUploadLaterEvent $event): void
    {
        $data = $event->getApplicationAttachmentsUploadLaterData();
        $applicationAttachmentsData = $data->getApplicationAttachmentsData();

        foreach ($applicationAttachmentsData as $applicationAttachmentsDatum)
        {
            $application = $applicationAttachmentsDatum->getApplication();
            $applicationCamper = $applicationAttachmentsDatum->getApplicationCamper();
            $targetApplicationAttachmentsData = $applicationAttachmentsDatum->getApplicationAttachmentsData();
            $applicationAttachments = [];

            if ($application !== null)
            {
                $applicationAttachments = $application->getApplicationAttachments();
            }
            else if ($applicationCamper !== null)
            {
                $applicationAttachments = $applicationCamper->getApplicationAttachments();
            }

            foreach ($targetApplicationAttachmentsData as $index => $targetApplicationAttachmentData)
            {
                if (array_key_exists($index, $applicationAttachments))
                {
                    $applicationAttachment = $applicationAttachments[$index];
                    $event = new ApplicationAttachmentUpdateEvent($targetApplicationAttachmentData, $applicationAttachment);
                    $event->setIsFlush(false);
                    $this->eventDispatcher->dispatch($event, $event::NAME);
                }
            }
        }
    }

    #[AsEventListener(event: ApplicationAttachmentsUploadLaterEvent::NAME, priority: 100)]
    public function onUploadSaveApplication(ApplicationAttachmentsUploadLaterEvent $event): void
    {
        $application = $event->getApplication();
        $isFlush = $event->isFlush();
        $this->applicationRepository->saveApplication($application, $isFlush);
    }
}