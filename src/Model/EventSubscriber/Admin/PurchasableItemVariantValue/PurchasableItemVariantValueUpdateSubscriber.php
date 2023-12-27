<?php

namespace App\Model\EventSubscriber\Admin\PurchasableItemVariantValue;

use App\Model\Event\Admin\PurchasableItemVariantValue\PurchasableItemVariantValueUpdateEvent;
use App\Model\Repository\PurchasableItemVariantValueRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class PurchasableItemVariantValueUpdateSubscriber
{
    private PurchasableItemVariantValueRepositoryInterface $repository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(PurchasableItemVariantValueRepositoryInterface $repository,
                                DataTransferRegistryInterface                  $dataTransfer)
    {
        $this->repository = $repository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: PurchasableItemVariantValueUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(PurchasableItemVariantValueUpdateEvent $event): void
    {
        $data = $event->getPurchasableItemVariantValueData();
        $entity = $event->getPurchasableItemVariantValue();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: PurchasableItemVariantValueUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(PurchasableItemVariantValueUpdateEvent $event): void
    {
        $entity = $event->getPurchasableItemVariantValue();
        $isFlush = $event->isFlush();
        $this->repository->savePurchasableItemVariantValue($entity, $isFlush);
    }
}