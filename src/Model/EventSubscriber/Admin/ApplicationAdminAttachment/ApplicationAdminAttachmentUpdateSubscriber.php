<?php

namespace App\Model\EventSubscriber\Admin\ApplicationAdminAttachment;

use App\Model\Event\Admin\ApplicationAdminAttachment\ApplicationAdminAttachmentUpdateEvent;
use App\Model\Repository\ApplicationAdminAttachmentRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationAdminAttachmentUpdateSubscriber
{
    private ApplicationAdminAttachmentRepositoryInterface $repository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(ApplicationAdminAttachmentRepositoryInterface $repository,
                                DataTransferRegistryInterface                 $dataTransfer)
    {
        $this->repository = $repository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: ApplicationAdminAttachmentUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(ApplicationAdminAttachmentUpdateEvent $event): void
    {
        $data = $event->getApplicationAdminAttachmentUpdateData();
        $entity = $event->getApplicationAdminAttachment();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: ApplicationAdminAttachmentUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(ApplicationAdminAttachmentUpdateEvent $event): void
    {
        $entity = $event->getApplicationAdminAttachment();
        $isFlush = $event->isFlush();
        $this->repository->saveApplicationAdminAttachment($entity, $isFlush);
    }
}