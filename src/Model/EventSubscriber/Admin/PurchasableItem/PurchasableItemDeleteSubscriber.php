<?php

namespace App\Model\EventSubscriber\Admin\PurchasableItem;

use App\Model\Event\Admin\PurchasableItem\PurchasableItemDeleteEvent;
use App\Model\Repository\PurchasableItemRepositoryInterface;
use App\Model\Service\PurchasableItem\PurchasableItemImageFilesystemInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class PurchasableItemDeleteSubscriber
{
    private PurchasableItemImageFilesystemInterface $purchasableItemImageFilesystem;

    private PurchasableItemRepositoryInterface $repository;

    public function __construct(PurchasableItemImageFilesystemInterface $purchasableItemImageFilesystem,
                                PurchasableItemRepositoryInterface      $repository)
    {
        $this->purchasableItemImageFilesystem = $purchasableItemImageFilesystem;
        $this->repository = $repository;
    }

    #[AsEventListener(event: PurchasableItemDeleteEvent::NAME, priority: 200)]
    public function onDeleteRemoveFile(PurchasableItemDeleteEvent $event): void
    {
        $entity = $event->getPurchasableItem();
        $this->purchasableItemImageFilesystem->removeImageFile($entity);
    }

    #[AsEventListener(event: PurchasableItemDeleteEvent::NAME, priority: 100)]
    public function onDeleteRemoveEntity(PurchasableItemDeleteEvent $event): void
    {
        $entity = $event->getPurchasableItem();
        $isFlush = $event->isFlush();
        $this->repository->removePurchasableItem($entity, $isFlush);
    }
}