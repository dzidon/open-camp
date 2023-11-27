<?php

namespace App\Model\EventSubscriber\Admin\PurchasableItemVariantValue;

use App\Model\Event\Admin\PurchasableItemVariantValue\PurchasableItemVariantValueDeleteEvent;
use App\Model\Repository\PurchasableItemVariantValueRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class PurchasableItemVariantValueDeleteSubscriber
{
    private PurchasableItemVariantValueRepositoryInterface $repository;

    public function __construct(PurchasableItemVariantValueRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[AsEventListener(event: PurchasableItemVariantValueDeleteEvent::NAME)]
    public function onDeleteRemoveEntity(PurchasableItemVariantValueDeleteEvent $event): void
    {
        $entity = $event->getPurchasableItemVariantValue();
        $this->repository->removePurchasableItemVariantValue($entity, true);
    }
}