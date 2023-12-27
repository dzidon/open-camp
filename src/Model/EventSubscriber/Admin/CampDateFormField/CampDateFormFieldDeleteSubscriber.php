<?php

namespace App\Model\EventSubscriber\Admin\CampDateFormField;

use App\Model\Event\Admin\CampDateFormField\CampDateFormFieldDeleteEvent;
use App\Model\Repository\CampDateFormFieldRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class CampDateFormFieldDeleteSubscriber
{
    private CampDateFormFieldRepositoryInterface $repository;

    public function __construct(CampDateFormFieldRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[AsEventListener(event: CampDateFormFieldDeleteEvent::NAME, priority: 200)]
    public function onDeleteRemoveEntity(CampDateFormFieldDeleteEvent $event): void
    {
        $entity = $event->getCampDateFormField();
        $flush = $event->isFlush();
        $this->repository->removeCampDateFormField($entity, $flush);
    }

    #[AsEventListener(event: CampDateFormFieldDeleteEvent::NAME, priority: 100)]
    public function onDeleteRemoveFromCampDateCollection(CampDateFormFieldDeleteEvent $event): void
    {
        $entity = $event->getCampDateFormField();
        $campDate = $entity->getCampDate();
        $campDate->removeCampDateFormField($entity);
    }
}