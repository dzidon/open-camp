<?php

namespace App\Model\EventSubscriber\Admin\PurchasableItemVariant;

use App\Model\Event\Admin\PurchasableItemVariant\PurchasableItemVariantDeleteEvent;
use App\Model\Repository\PurchasableItemVariantRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class PurchasableItemVariantDeleteSubscriber
{
    private PurchasableItemVariantRepositoryInterface $repository;

    public function __construct(PurchasableItemVariantRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[AsEventListener(event: PurchasableItemVariantDeleteEvent::NAME)]
    public function onDeleteRemoveEntity(PurchasableItemVariantDeleteEvent $event): void
    {
        $entity = $event->getPurchasableItemVariant();
        $isFlush = $event->isFlush();
        $this->repository->removePurchasableItemVariant($entity, $isFlush);
    }
}