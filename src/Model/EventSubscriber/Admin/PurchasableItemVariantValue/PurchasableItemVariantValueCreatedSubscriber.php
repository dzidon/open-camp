<?php

namespace App\Model\EventSubscriber\Admin\PurchasableItemVariantValue;

use App\Model\Event\Admin\PurchasableItemVariantValue\PurchasableItemVariantValueCreatedEvent;
use App\Model\Repository\PurchasableItemVariantValueRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class PurchasableItemVariantValueCreatedSubscriber
{
    private PurchasableItemVariantValueRepositoryInterface $repository;

    public function __construct(PurchasableItemVariantValueRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[AsEventListener(event: PurchasableItemVariantValueCreatedEvent::NAME)]
    public function onCreatedSaveEntity(PurchasableItemVariantValueCreatedEvent $event): void
    {
        $entity = $event->getPurchasableItemVariantValue();
        $this->repository->savePurchasableItemVariantValue($entity, true);
    }
}