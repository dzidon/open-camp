<?php

namespace App\Model\EventSubscriber\Admin\TripLocationPath;

use App\Model\Event\Admin\TripLocationPath\TripLocationPathCreatedEvent;
use App\Model\Repository\TripLocationPathRepositoryInterface;
use App\Model\Repository\TripLocationRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class TripLocationPathCreatedSubscriber
{
    private TripLocationPathRepositoryInterface $tripLocationPathRepository;

    private TripLocationRepositoryInterface $tripLocationRepository;

    public function __construct(TripLocationPathRepositoryInterface $tripLocationPathRepository,
                                TripLocationRepositoryInterface     $tripLocationRepository)
    {
        $this->tripLocationPathRepository = $tripLocationPathRepository;
        $this->tripLocationRepository = $tripLocationRepository;
    }

    #[AsEventListener(event: TripLocationPathCreatedEvent::NAME)]
    public function onCreatedSave(TripLocationPathCreatedEvent $event): void
    {
        $tripLocationPath = $event->getTripLocationPath();
        $tripLocations = $event->getTripLocations();

        foreach ($tripLocations as $tripLocation)
        {
            $this->tripLocationRepository->saveTripLocation($tripLocation, false);
        }

        $this->tripLocationPathRepository->saveTripLocationPath($tripLocationPath, true);
    }
}