<?php

namespace App\Model\EventSubscriber\Admin\PurchasableItemVariant;

use App\Model\Event\Admin\PurchasableItemVariant\PurchasableItemVariantUpdateEvent;
use App\Model\Repository\PurchasableItemVariantRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class PurchasableItemVariantUpdateSubscriber
{
    private PurchasableItemVariantRepositoryInterface $repository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(PurchasableItemVariantRepositoryInterface $repository,
                                DataTransferRegistryInterface             $dataTransfer)
    {
        $this->repository = $repository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: PurchasableItemVariantUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(PurchasableItemVariantUpdateEvent $event): void
    {
        $data = $event->getPurchasableItemVariantData();
        $entity = $event->getPurchasableItemVariant();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: PurchasableItemVariantUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(PurchasableItemVariantUpdateEvent $event): void
    {
        $entity = $event->getPurchasableItemVariant();
        $isFlush = $event->isFlush();
        $this->repository->savePurchasableItemVariant($entity, $isFlush);
    }
}