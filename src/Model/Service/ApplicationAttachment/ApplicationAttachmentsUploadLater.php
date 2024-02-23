<?php

namespace App\Model\Service\ApplicationAttachment;

use App\Library\Data\User\ApplicationAttachmentsUploadLaterData;
use App\Model\Event\User\ApplicationAttachment\ApplicationAttachmentUpdateEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @inheritDoc
 */
class ApplicationAttachmentsUploadLater implements ApplicationAttachmentsUploadLaterInterface
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @inheritDoc
     */
    public function uploadApplicationAttachments(ApplicationAttachmentsUploadLaterData $data): void
    {
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
}