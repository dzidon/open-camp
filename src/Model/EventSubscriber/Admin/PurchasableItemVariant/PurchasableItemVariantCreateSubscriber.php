<?php

namespace App\Model\EventSubscriber\Admin\PurchasableItemVariant;

use App\Model\Entity\PurchasableItemVariant;
use App\Model\Event\Admin\PurchasableItemVariant\PurchasableItemVariantCreateEvent;
use App\Model\Event\Admin\PurchasableItemVariantValue\PurchasableItemVariantValueCreateEvent;
use App\Model\Repository\PurchasableItemVariantRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PurchasableItemVariantCreateSubscriber
{
    private DataTransferRegistryInterface $dataTransfer;

    private EventDispatcherInterface $eventDispatcher;

    private PurchasableItemVariantRepositoryInterface $repository;

    public function __construct(DataTransferRegistryInterface             $dataTransfer,
                                EventDispatcherInterface                  $eventDispatcher,
                                PurchasableItemVariantRepositoryInterface $repository)
    {
        $this->dataTransfer = $dataTransfer;
        $this->eventDispatcher = $eventDispatcher;
        $this->repository = $repository;
    }

    #[AsEventListener(event: PurchasableItemVariantCreateEvent::NAME, priority: 200)]
    public function onCreateFillEntity(PurchasableItemVariantCreateEvent $event): void
    {
        $creationData = $event->getPurchasableItemVariantCreationData();
        $purchasableItemVariantData = $creationData->getPurchasableItemVariantData();
        $purchasableItemVariantValuesData = $creationData->getPurchasableItemVariantValuesData();

        $purchasableItemVariant = new PurchasableItemVariant($purchasableItemVariantData->getName(), $purchasableItemVariantData->getPriority(), $purchasableItemVariantData->getPurchasableItem());
        $this->dataTransfer->fillEntity($purchasableItemVariantData, $purchasableItemVariant);
        $event->setPurchasableItemVariant($purchasableItemVariant);

        foreach ($purchasableItemVariantValuesData as $purchasableItemVariantValueData)
        {
            $event = new PurchasableItemVariantValueCreateEvent($purchasableItemVariantValueData, $purchasableItemVariant);
            $event->setIsFlush(false);
            $this->eventDispatcher->dispatch($event, $event::NAME);
        }
    }

    #[AsEventListener(event: PurchasableItemVariantCreateEvent::NAME, priority: 100)]
    public function onCreateSave(PurchasableItemVariantCreateEvent $event): void
    {
        $purchasableItemVariant = $event->getPurchasableItemVariant();
        $isFlush = $event->isFlush();
        $this->repository->savePurchasableItemVariant($purchasableItemVariant, $isFlush);
    }
}