<?php

namespace App\Model\EventSubscriber\Admin\CampDateAttachmentConfig;

use App\Model\Entity\CampDateAttachmentConfig;
use App\Model\Event\Admin\CampDateAttachmentConfig\CampDateAttachmentConfigCreateEvent;
use App\Model\Repository\CampDateAttachmentConfigRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class CampDateAttachmentConfigCreateSubscriber
{
    private CampDateAttachmentConfigRepositoryInterface $repository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(CampDateAttachmentConfigRepositoryInterface $repository, DataTransferRegistryInterface $dataTransfer)
    {
        $this->repository = $repository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: CampDateAttachmentConfigCreateEvent::NAME, priority: 200)]
    public function onCreateFillEntity(CampDateAttachmentConfigCreateEvent $event): void
    {
        $campDate = $event->getCampDate();
        $data = $event->getCampDateAttachmentConfigData();
        $entity = new CampDateAttachmentConfig($campDate, $data->getAttachmentConfig(), $data->getPriority());
        $this->dataTransfer->fillEntity($data, $entity);
        $event->setCampDateAttachmentConfig($entity);
    }

    #[AsEventListener(event: CampDateAttachmentConfigCreateEvent::NAME, priority: 100)]
    public function onCreateSaveEntity(CampDateAttachmentConfigCreateEvent $event): void
    {
        $entity = $event->getCampDateAttachmentConfig();
        $flush = $event->isFlush();
        $this->repository->saveCampDateAttachmentConfig($entity, $flush);
    }
}