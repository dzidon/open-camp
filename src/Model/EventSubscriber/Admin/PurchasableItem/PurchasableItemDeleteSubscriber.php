<?php

namespace App\Model\EventSubscriber\Admin\PurchasableItem;

use App\Model\Event\Admin\PurchasableItem\PurchasableItemDeleteEvent;
use App\Model\Repository\PurchasableItemRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class PurchasableItemDeleteSubscriber
{
    private PurchasableItemRepositoryInterface $repository;

    public function __construct(PurchasableItemRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[AsEventListener(event: PurchasableItemDeleteEvent::NAME)]
    public function onDeleteRemoveEntity(PurchasableItemDeleteEvent $event): void
    {
        $entity = $event->getPurchasableItem();
        $this->repository->removePurchasableItem($entity, true);
    }
}