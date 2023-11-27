<?php

namespace App\Model\EventSubscriber\Admin\TripLocation;

use App\Model\Event\Admin\TripLocation\TripLocationCreatedEvent;
use App\Model\Repository\TripLocationRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class TripLocationCreatedSubscriber
{
    private TripLocationRepositoryInterface $repository;

    public function __construct(TripLocationRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[AsEventListener(event: TripLocationCreatedEvent::NAME)]
    public function onCreatedSaveEntity(TripLocationCreatedEvent $event): void
    {
        $entity = $event->getTripLocation();
        $this->repository->saveTripLocation($entity, true);
    }
}