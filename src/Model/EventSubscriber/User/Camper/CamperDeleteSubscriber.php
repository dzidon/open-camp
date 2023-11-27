<?php

namespace App\Model\EventSubscriber\User\Camper;

use App\Model\Event\User\Camper\CamperDeleteEvent;
use App\Model\Repository\CamperRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class CamperDeleteSubscriber
{
    private CamperRepositoryInterface $repository;

    public function __construct(CamperRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[AsEventListener(event: CamperDeleteEvent::NAME)]
    public function onDeleteRemoveEntity(CamperDeleteEvent $event): void
    {
        $entity = $event->getCamper();
        $this->repository->removeCamper($entity, true);
    }
}