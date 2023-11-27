<?php

namespace App\Model\EventSubscriber\Admin\PurchasableItem;

use App\Model\Event\Admin\PurchasableItem\PurchasableItemCreatedEvent;
use App\Model\Repository\PurchasableItemRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class PurchasableItemCreatedSubscriber
{
    private PurchasableItemRepositoryInterface $repository;

    public function __construct(PurchasableItemRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[AsEventListener(event: PurchasableItemCreatedEvent::NAME, priority: 100)]
    public function onCreatedSaveEntity(PurchasableItemCreatedEvent $event): void
    {
        $entity = $event->getPurchasableItem();
        $this->repository->savePurchasableItem($entity, true);
    }
}