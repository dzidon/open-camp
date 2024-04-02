<?php

namespace App\Model\EventSubscriber\Admin\ApplicationPurchasableItemInstance;

use App\Model\Event\Admin\ApplicationPurchasableItemInstance\ApplicationPurchasableItemInstanceDeleteEvent;
use App\Model\Repository\ApplicationPurchasableItemInstanceRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationPurchasableItemInstanceDeleteSubscriber
{
    private ApplicationPurchasableItemInstanceRepositoryInterface $repository;

    public function __construct(ApplicationPurchasableItemInstanceRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[AsEventListener(event: ApplicationPurchasableItemInstanceDeleteEvent::NAME, priority: 200)]
    public function onDeleteRemoveEntity(ApplicationPurchasableItemInstanceDeleteEvent $event): void
    {
        $entity = $event->getApplicationPurchasableItemInstance();
        $flush = $event->isFlush();
        $this->repository->removeApplicationPurchasableItemInstance($entity, $flush);
    }

    #[AsEventListener(event: ApplicationPurchasableItemInstanceDeleteEvent::NAME, priority: 100)]
    public function onDeleteRemoveFromCampDateCollection(ApplicationPurchasableItemInstanceDeleteEvent $event): void
    {
        $entity = $event->getApplicationPurchasableItemInstance();
        $applicationPurchasableItem = $entity->getApplicationPurchasableItem();
        $applicationPurchasableItem->removeApplicationPurchasableItemInstance($entity);
    }
}