<?php

namespace App\Model\EventSubscriber\Admin\CampDateUser;

use App\Model\Event\Admin\CampDateUser\CampDateUserDeleteEvent;
use App\Model\Repository\CampDateUserRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class CampDateUserDeleteSubscriber
{
    private CampDateUserRepositoryInterface $repository;

    public function __construct(CampDateUserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[AsEventListener(event: CampDateUserDeleteEvent::NAME, priority: 200)]
    public function onDeleteRemoveEntity(CampDateUserDeleteEvent $event): void
    {
        $entity = $event->getCampDateUser();
        $flush = $event->isFlush();
        $this->repository->removeCampDateUser($entity, $flush);
    }

    #[AsEventListener(event: CampDateUserDeleteEvent::NAME, priority: 100)]
    public function onDeleteRemoveFromCampDateCollection(CampDateUserDeleteEvent $event): void
    {
        $entity = $event->getCampDateUser();
        $campDate = $entity->getCampDate();
        $campDate->removeCampDateUser($entity);
    }
}