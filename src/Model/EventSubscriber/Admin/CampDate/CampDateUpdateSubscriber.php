<?php

namespace App\Model\EventSubscriber\Admin\CampDate;

use App\Model\Event\Admin\CampDate\CampDateUpdateEvent;
use App\Model\Repository\CampDateRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class CampDateUpdateSubscriber
{
    private CampDateRepositoryInterface $repository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(CampDateRepositoryInterface   $repository,
                                DataTransferRegistryInterface $dataTransfer)
    {
        $this->repository = $repository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: CampDateUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(CampDateUpdateEvent $event): void
    {
        $data = $event->getCampDateData();
        $entity = $event->getCampDate();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: CampDateUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(CampDateUpdateEvent $event): void
    {
        $entity = $event->getCampDate();
        $this->repository->saveCampDate($entity, true);
    }
}