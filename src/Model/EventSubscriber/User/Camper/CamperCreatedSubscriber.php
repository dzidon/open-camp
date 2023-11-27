<?php

namespace App\Model\EventSubscriber\User\Camper;

use App\Model\Event\User\Camper\CamperCreatedEvent;
use App\Model\Repository\CamperRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class CamperCreatedSubscriber
{
    private CamperRepositoryInterface $repository;

    public function __construct(CamperRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[AsEventListener(event: CamperCreatedEvent::NAME)]
    public function onCreatedSaveEntity(CamperCreatedEvent $event): void
    {
        $entity = $event->getCamper();
        $this->repository->saveCamper($entity, true);
    }
}