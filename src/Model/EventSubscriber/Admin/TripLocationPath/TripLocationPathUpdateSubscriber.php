<?php

namespace App\Model\EventSubscriber\Admin\TripLocationPath;

use App\Model\Event\Admin\TripLocationPath\TripLocationPathUpdateEvent;
use App\Model\Repository\TripLocationPathRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class TripLocationPathUpdateSubscriber
{
    private TripLocationPathRepositoryInterface $repository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(TripLocationPathRepositoryInterface $repository,
                                DataTransferRegistryInterface             $dataTransfer)
    {
        $this->repository = $repository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: TripLocationPathUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(TripLocationPathUpdateEvent $event): void
    {
        $data = $event->getTripLocationPathData();
        $entity = $event->getTripLocationPath();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: TripLocationPathUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(TripLocationPathUpdateEvent $event): void
    {
        $entity = $event->getTripLocationPath();
        $this->repository->saveTripLocationPath($entity, true);
    }
}