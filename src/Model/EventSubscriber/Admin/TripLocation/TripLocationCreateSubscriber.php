<?php

namespace App\Model\EventSubscriber\Admin\TripLocation;

use App\Model\Entity\TripLocation;
use App\Model\Event\Admin\TripLocation\TripLocationCreateEvent;
use App\Model\Repository\TripLocationRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class TripLocationCreateSubscriber
{
    private DataTransferRegistryInterface $dataTransfer;

    private TripLocationRepositoryInterface $repository;

    public function __construct(DataTransferRegistryInterface $dataTransfer, TripLocationRepositoryInterface $repository)
    {
        $this->dataTransfer = $dataTransfer;
        $this->repository = $repository;
    }

    #[AsEventListener(event: TripLocationCreateEvent::NAME, priority: 200)]
    public function onCreateFillEntity(TripLocationCreateEvent $event): void
    {
        $tripLocationPath = $event->getTripLocationPath();
        $data = $event->getTripLocationData();
        $entity = new TripLocation($data->getName(), $data->getPrice(), $data->getPriority(), $tripLocationPath);
        $this->dataTransfer->fillEntity($data, $entity);
        $event->setTripLocation($entity);
    }

    #[AsEventListener(event: TripLocationCreateEvent::NAME, priority: 100)]
    public function onCreateSaveEntity(TripLocationCreateEvent $event): void
    {
        $entity = $event->getTripLocation();
        $isFlush = $event->isFlush();
        $this->repository->saveTripLocation($entity, $isFlush);
    }
}