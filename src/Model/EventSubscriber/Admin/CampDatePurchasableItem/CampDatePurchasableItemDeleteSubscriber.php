<?php

namespace App\Model\EventSubscriber\Admin\CampDatePurchasableItem;

use App\Model\Event\Admin\CampDatePurchasableItem\CampDatePurchasableItemDeleteEvent;
use App\Model\Repository\CampDatePurchasableItemRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class CampDatePurchasableItemDeleteSubscriber
{
    private CampDatePurchasableItemRepositoryInterface $repository;

    public function __construct(CampDatePurchasableItemRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[AsEventListener(event: CampDatePurchasableItemDeleteEvent::NAME, priority: 200)]
    public function onDeleteRemoveEntity(CampDatePurchasableItemDeleteEvent $event): void
    {
        $entity = $event->getCampDatePurchasableItem();
        $flush = $event->isFlush();
        $this->repository->removeCampDatePurchasableItem($entity, $flush);
    }

    #[AsEventListener(event: CampDatePurchasableItemDeleteEvent::NAME, priority: 100)]
    public function onDeleteRemoveFromCampDateCollection(CampDatePurchasableItemDeleteEvent $event): void
    {
        $entity = $event->getCampDatePurchasableItem();
        $campDate = $entity->getCampDate();
        $campDate->removeCampDatePurchasableItem($entity);
    }
}