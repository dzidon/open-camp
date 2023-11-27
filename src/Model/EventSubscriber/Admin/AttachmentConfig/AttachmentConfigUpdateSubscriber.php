<?php

namespace App\Model\EventSubscriber\Admin\AttachmentConfig;

use App\Model\Event\Admin\AttachmentConfig\AttachmentConfigUpdateEvent;
use App\Model\Repository\AttachmentConfigRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class AttachmentConfigUpdateSubscriber
{
    private AttachmentConfigRepositoryInterface $repository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(AttachmentConfigRepositoryInterface $repository,
                                DataTransferRegistryInterface       $dataTransfer)
    {
        $this->repository = $repository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: AttachmentConfigUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(AttachmentConfigUpdateEvent $event): void
    {
        $data = $event->getAttachmentConfigData();
        $entity = $event->getAttachmentConfig();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: AttachmentConfigUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(AttachmentConfigUpdateEvent $event): void
    {
        $entity = $event->getAttachmentConfig();
        $this->repository->saveAttachmentConfig($entity, true);
    }
}