<?php

namespace App\Model\EventSubscriber\Admin\ApplicationAttachment;

use App\Model\Entity\ApplicationAttachment;
use App\Model\Event\Admin\ApplicationAttachment\ApplicationAttachmentCreateEvent;
use App\Model\Repository\ApplicationAttachmentRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationAttachmentCreateSubscriber
{
    private ApplicationAttachmentRepositoryInterface $applicationAttachmentRepository;

    public function __construct(ApplicationAttachmentRepositoryInterface $applicationAttachmentRepository)
    {
        $this->applicationAttachmentRepository = $applicationAttachmentRepository;
    }

    #[AsEventListener(event: ApplicationAttachmentCreateEvent::NAME, priority: 300)]
    public function onCreateFillEntity(ApplicationAttachmentCreateEvent $event): void
    {
        $data = $event->getApplicationAttachmentData();
        $application = $event->getApplication();
        $applicationCamper = $event->getApplicationCamper();

        $applicationAttachment = new ApplicationAttachment(
            $data->getLabel(),
            $data->getHelp(),
            $data->getMaxSize(),
            $data->getRequiredType(),
            $data->getExtensions(),
            $data->getPriority(),
            $application,
            $applicationCamper
        );

        $event->setApplicationAttachment($applicationAttachment);
    }

    #[AsEventListener(event: ApplicationAttachmentCreateEvent::NAME, priority: 100)]
    public function onCreateSaveEntity(ApplicationAttachmentCreateEvent $event): void
    {
        $applicationAttachment = $event->getApplicationAttachment();
        $isFlush = $event->isFlush();
        $this->applicationAttachmentRepository->saveApplicationAttachment($applicationAttachment, $isFlush);
    }
}