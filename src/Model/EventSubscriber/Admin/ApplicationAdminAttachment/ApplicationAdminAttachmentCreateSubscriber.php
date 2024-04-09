<?php

namespace App\Model\EventSubscriber\Admin\ApplicationAdminAttachment;

use App\Model\Event\Admin\ApplicationAdminAttachment\ApplicationAdminAttachmentCreateEvent;
use App\Model\Repository\ApplicationAdminAttachmentRepositoryInterface;
use App\Model\Service\ApplicationAdminAttachment\ApplicationAdminAttachmentFactoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationAdminAttachmentCreateSubscriber
{
    private ApplicationAdminAttachmentRepositoryInterface $applicationAdminAttachmentRepository;

    private ApplicationAdminAttachmentFactoryInterface $applicationAdminAttachmentFactory;

    public function __construct(ApplicationAdminAttachmentRepositoryInterface $applicationAdminAttachmentRepository,
                                ApplicationAdminAttachmentFactoryInterface    $applicationAdminAttachmentFactory)
    {
        $this->applicationAdminAttachmentRepository = $applicationAdminAttachmentRepository;
        $this->applicationAdminAttachmentFactory = $applicationAdminAttachmentFactory;
    }

    #[AsEventListener(event: ApplicationAdminAttachmentCreateEvent::NAME, priority: 200)]
    public function onCreateInstantiateEntity(ApplicationAdminAttachmentCreateEvent $event): void
    {
        $data = $event->getApplicationAdminAttachmentCreateData();
        $label = $data->getLabel();
        $file = $data->getFile();
        $application = $event->getApplication();

        $applicationAdminAttachment = $this->applicationAdminAttachmentFactory->createApplicationAdminAttachment(
            $file,
            $application,
            $label
        );

        $event->setApplicationAdminAttachment($applicationAdminAttachment);
    }

    #[AsEventListener(event: ApplicationAdminAttachmentCreateEvent::NAME, priority: 100)]
    public function onCreateSaveEntity(ApplicationAdminAttachmentCreateEvent $event): void
    {
        $entity = $event->getApplicationAdminAttachment();
        $isFlush = $event->isFlush();
        $this->applicationAdminAttachmentRepository->saveApplicationAdminAttachment($entity, $isFlush);
    }
}