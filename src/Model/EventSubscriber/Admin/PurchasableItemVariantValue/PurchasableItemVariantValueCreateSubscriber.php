<?php

namespace App\Model\EventSubscriber\Admin\PurchasableItemVariantValue;

use App\Model\Entity\PurchasableItemVariantValue;
use App\Model\Event\Admin\PurchasableItemVariantValue\PurchasableItemVariantValueCreateEvent;
use App\Model\Repository\PurchasableItemVariantValueRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class PurchasableItemVariantValueCreateSubscriber
{
    private DataTransferRegistryInterface $dataTransfer;

    private PurchasableItemVariantValueRepositoryInterface $repository;

    public function __construct(DataTransferRegistryInterface $dataTransfer, PurchasableItemVariantValueRepositoryInterface $repository)
    {
        $this->dataTransfer = $dataTransfer;
        $this->repository = $repository;
    }

    #[AsEventListener(event: PurchasableItemVariantValueCreateEvent::NAME, priority: 200)]
    public function onCreateFillEntity(PurchasableItemVariantValueCreateEvent $event): void
    {
        $data = $event->getPurchasableItemVariantValueData();
        $purchasableItemVariant = $event->getPurchasableItemVariant();
        $entity = new PurchasableItemVariantValue($data->getName(), $data->getPriority(), $purchasableItemVariant);
        $this->dataTransfer->fillEntity($data, $entity);
        $event->setPurchasableItemVariantValue($entity);
    }

    #[AsEventListener(event: PurchasableItemVariantValueCreateEvent::NAME, priority: 100)]
    public function onCreateSaveEntity(PurchasableItemVariantValueCreateEvent $event): void
    {
        $entity = $event->getPurchasableItemVariantValue();
        $isFlush = $event->isFlush();
        $this->repository->savePurchasableItemVariantValue($entity, $isFlush);
    }
}