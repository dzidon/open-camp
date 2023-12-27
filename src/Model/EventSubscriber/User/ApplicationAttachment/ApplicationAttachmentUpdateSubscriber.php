<?php

namespace App\Model\EventSubscriber\User\ApplicationAttachment;

use App\Model\Event\User\ApplicationAttachment\ApplicationAttachmentUpdateEvent;
use App\Model\Repository\ApplicationAttachmentRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationAttachmentUpdateSubscriber
{
    private ApplicationAttachmentRepositoryInterface $applicationAttachmentRepository;

    public function __construct(ApplicationAttachmentRepositoryInterface $applicationAttachmentRepository)
    {
        $this->applicationAttachmentRepository = $applicationAttachmentRepository;
    }

    #[AsEventListener(event: ApplicationAttachmentUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(ApplicationAttachmentUpdateEvent $event): void
    {
        $entity = $event->getApplicationAttachment();
        $isFlush = $event->isFlush();
        $this->applicationAttachmentRepository->saveApplicationAttachment($entity, $isFlush);
    }
}