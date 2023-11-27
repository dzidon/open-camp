<?php

namespace App\Model\EventSubscriber\Admin\TripLocation;

use App\Model\Event\Admin\TripLocation\TripLocationUpdateEvent;
use App\Model\Repository\TripLocationRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class TripLocationUpdateSubscriber
{
    private TripLocationRepositoryInterface $repository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(TripLocationRepositoryInterface $repository,
                                DataTransferRegistryInterface       $dataTransfer)
    {
        $this->repository = $repository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: TripLocationUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(TripLocationUpdateEvent $event): void
    {
        $data = $event->getTripLocationData();
        $entity = $event->getTripLocation();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: TripLocationUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(TripLocationUpdateEvent $event): void
    {
        $entity = $event->getTripLocation();
        $this->repository->saveTripLocation($entity, true);
    }
}