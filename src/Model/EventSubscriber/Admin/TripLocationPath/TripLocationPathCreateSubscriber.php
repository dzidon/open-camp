<?php

namespace App\Model\EventSubscriber\Admin\TripLocationPath;

use App\Model\Entity\TripLocationPath;
use App\Model\Event\Admin\TripLocation\TripLocationCreateEvent;
use App\Model\Event\Admin\TripLocationPath\TripLocationPathCreateEvent;
use App\Model\Repository\TripLocationPathRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TripLocationPathCreateSubscriber
{
    private DataTransferRegistryInterface $dataTransfer;

    private EventDispatcherInterface $eventDispatcher;

    private TripLocationPathRepositoryInterface $repository;

    public function __construct(DataTransferRegistryInterface       $dataTransfer,
                                EventDispatcherInterface            $eventDispatcher,
                                TripLocationPathRepositoryInterface $repository)
    {
        $this->dataTransfer = $dataTransfer;
        $this->eventDispatcher = $eventDispatcher;
        $this->repository = $repository;
    }

    #[AsEventListener(event: TripLocationPathCreateEvent::NAME, priority: 200)]
    public function onCreateFillEntity(TripLocationPathCreateEvent $event): void
    {
        $creationData = $event->getTripLocationPathCreationData();
        $tripLocationPathData = $creationData->getTripLocationPathData();
        $tripLocationsData = $creationData->getTripLocationsData();

        $tripLocationPath = new TripLocationPath($tripLocationPathData->getName());
        $this->dataTransfer->fillEntity($tripLocationPathData, $tripLocationPath);
        $event->setTripLocationPath($tripLocationPath);

        foreach ($tripLocationsData as $tripLocationData)
        {
            $event = new TripLocationCreateEvent($tripLocationData, $tripLocationPath);
            $event->setIsFlush(false);
            $this->eventDispatcher->dispatch($event, $event::NAME);
        }
    }

    #[AsEventListener(event: TripLocationPathCreateEvent::NAME, priority: 100)]
    public function onCreateSave(TripLocationPathCreateEvent $event): void
    {
        $tripLocationPath = $event->getTripLocationPath();
        $isFlush = $event->isFlush();
        $this->repository->saveTripLocationPath($tripLocationPath, $isFlush);
    }
}