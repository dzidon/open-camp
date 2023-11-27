<?php

namespace App\Model\EventSubscriber\Admin\Camp;

use App\Model\Event\Admin\Camp\CampUpdateEvent;
use App\Model\Repository\CampRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class CampUpdateSubscriber
{
    private CampRepositoryInterface $repository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(CampRepositoryInterface       $repository,
                                DataTransferRegistryInterface $dataTransfer)
    {
        $this->repository = $repository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: CampUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(CampUpdateEvent $event): void
    {
        $data = $event->getCampData();
        $entity = $event->getCamp();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: CampUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(CampUpdateEvent $event): void
    {
        $entity = $event->getCamp();
        $this->repository->saveCamp($entity, true);
    }
}