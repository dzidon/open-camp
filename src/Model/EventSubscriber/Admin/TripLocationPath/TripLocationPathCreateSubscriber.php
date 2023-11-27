<?php

namespace App\Model\EventSubscriber\Admin\TripLocationPath;

use App\Model\Entity\TripLocation;
use App\Model\Entity\TripLocationPath;
use App\Model\Event\Admin\TripLocationPath\TripLocationPathCreatedEvent;
use App\Model\Event\Admin\TripLocationPath\TripLocationPathCreateEvent;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TripLocationPathCreateSubscriber
{
    private DataTransferRegistryInterface $dataTransfer;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(DataTransferRegistryInterface $dataTransfer, EventDispatcherInterface $eventDispatcher)
    {
        $this->dataTransfer = $dataTransfer;
        $this->eventDispatcher = $eventDispatcher;
    }

    #[AsEventListener(event: TripLocationPathCreateEvent::NAME)]
    public function onCreateFillEntity(TripLocationPathCreateEvent $event): void
    {
        $creationData = $event->getTripLocationPathCreationData();
        $tripLocationPathData = $creationData->getTripLocationPathData();

        $tripLocationPath = new TripLocationPath($tripLocationPathData->getName());
        $this->dataTransfer->fillEntity($tripLocationPathData, $tripLocationPath);

        $tripLocations = [];
        $tripLocationsData = $creationData->getTripLocationsData();
        foreach ($tripLocationsData as $tripLocationData)
        {
            $tripLocation = new TripLocation($tripLocationData->getName(), $tripLocationData->getPrice(), $tripLocationData->getPriority(), $tripLocationPath);
            $this->dataTransfer->fillEntity($tripLocationData, $tripLocation);
            $tripLocations[] = $tripLocation;
        }

        $event = new TripLocationPathCreatedEvent($creationData, $tripLocationPath, $tripLocations);
        $this->eventDispatcher->dispatch($event, TripLocationPathCreatedEvent::NAME);
    }
}