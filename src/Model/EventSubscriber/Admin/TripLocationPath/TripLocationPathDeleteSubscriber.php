<?php

namespace App\Model\EventSubscriber\Admin\TripLocationPath;

use App\Model\Event\Admin\TripLocationPath\TripLocationPathDeleteEvent;
use App\Model\Repository\TripLocationPathRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class TripLocationPathDeleteSubscriber
{
    private TripLocationPathRepositoryInterface $repository;

    public function __construct(TripLocationPathRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[AsEventListener(event: TripLocationPathDeleteEvent::NAME)]
    public function onDeleteRemoveEntity(TripLocationPathDeleteEvent $event): void
    {
        $entity = $event->getTripLocationPath();
        $this->repository->removeTripLocationPath($entity, true);
    }
}