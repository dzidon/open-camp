<?php

namespace App\Model\EventSubscriber\Admin\TripLocation;

use App\Model\Event\Admin\TripLocation\TripLocationDeleteEvent;
use App\Model\Repository\TripLocationRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class TripLocationDeleteSubscriber
{
    private TripLocationRepositoryInterface $repository;

    public function __construct(TripLocationRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[AsEventListener(event: TripLocationDeleteEvent::NAME)]
    public function onDeleteRemoveEntity(TripLocationDeleteEvent $event): void
    {
        $entity = $event->getTripLocation();
        $isFlush = $event->isFlush();
        $this->repository->removeTripLocation($entity, $isFlush);
    }
}