<?php

namespace App\Model\EventSubscriber\Admin\PurchasableItem;

use App\Model\Event\Admin\PurchasableItem\PurchasableItemUpdateEvent;
use App\Model\Repository\PurchasableItemRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class PurchasableItemUpdateSubscriber
{
    private PurchasableItemRepositoryInterface $repository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(PurchasableItemRepositoryInterface $repository,
                                DataTransferRegistryInterface      $dataTransfer)
    {
        $this->repository = $repository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: PurchasableItemUpdateEvent::NAME, priority: 300)]
    public function onUpdateFillEntity(PurchasableItemUpdateEvent $event): void
    {
        $data = $event->getPurchasableItemData();
        $entity = $event->getPurchasableItem();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: PurchasableItemUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(PurchasableItemUpdateEvent $event): void
    {
        $entity = $event->getPurchasableItem();
        $this->repository->savePurchasableItem($entity, true);
    }
}