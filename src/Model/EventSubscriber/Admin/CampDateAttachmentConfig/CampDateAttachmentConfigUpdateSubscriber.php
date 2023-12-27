<?php

namespace App\Model\EventSubscriber\Admin\CampDateAttachmentConfig;

use App\Model\Event\Admin\CampDateAttachmentConfig\CampDateAttachmentConfigUpdateEvent;
use App\Model\Repository\CampDateAttachmentConfigRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class CampDateAttachmentConfigUpdateSubscriber
{
    private CampDateAttachmentConfigRepositoryInterface $repository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(CampDateAttachmentConfigRepositoryInterface $repository,
                                DataTransferRegistryInterface        $dataTransfer)
    {
        $this->repository = $repository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: CampDateAttachmentConfigUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(CampDateAttachmentConfigUpdateEvent $event): void
    {
        $data = $event->getCampDateAttachmentConfigData();
        $entity = $event->getCampDateAttachmentConfig();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: CampDateAttachmentConfigUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(CampDateAttachmentConfigUpdateEvent $event): void
    {
        $entity = $event->getCampDateAttachmentConfig();
        $flush = $event->isFlush();
        $this->repository->saveCampDateAttachmentConfig($entity, $flush);
    }
}