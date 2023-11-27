<?php

namespace App\Model\EventSubscriber\Admin\Camp;

use App\Model\Event\Admin\Camp\CampDeleteEvent;
use App\Model\Repository\CampRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class CampDeleteSubscriber
{
    private CampRepositoryInterface $repository;

    public function __construct(CampRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[AsEventListener(event: CampDeleteEvent::NAME)]
    public function onDeleteRemoveEntity(CampDeleteEvent $event): void
    {
        $entity = $event->getCamp();
        $this->repository->removeCamp($entity, true);
    }
}