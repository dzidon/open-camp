<?php

namespace App\Model\EventSubscriber\Admin\PurchasableItemVariant;

use App\Model\Entity\PurchasableItemVariant;
use App\Model\Entity\PurchasableItemVariantValue;
use App\Model\Event\Admin\PurchasableItemVariant\PurchasableItemVariantCreatedEvent;
use App\Model\Event\Admin\PurchasableItemVariant\PurchasableItemVariantCreateEvent;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PurchasableItemVariantCreateSubscriber
{
    private DataTransferRegistryInterface $dataTransfer;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(DataTransferRegistryInterface $dataTransfer, EventDispatcherInterface $eventDispatcher)
    {
        $this->dataTransfer = $dataTransfer;
        $this->eventDispatcher = $eventDispatcher;
    }

    #[AsEventListener(event: PurchasableItemVariantCreateEvent::NAME)]
    public function onCreateFillEntity(PurchasableItemVariantCreateEvent $event): void
    {
        $creationData = $event->getPurchasableItemVariantCreationData();
        $purchasableItemVariantData = $creationData->getPurchasableItemVariantData();

        $purchasableItemVariant = new PurchasableItemVariant($purchasableItemVariantData->getName(), $purchasableItemVariantData->getPriority(), $purchasableItemVariantData->getPurchasableItem());
        $this->dataTransfer->fillEntity($purchasableItemVariantData, $purchasableItemVariant);

        $purchasableItemVariantValues = [];
        $purchasableItemVariantValuesData = $creationData->getPurchasableItemVariantValuesData();
        foreach ($purchasableItemVariantValuesData as $purchasableItemVariantValueData)
        {
            $purchasableItemVariantValue = new PurchasableItemVariantValue($purchasableItemVariantValueData->getName(), $purchasableItemVariantValueData->getPriority(), $purchasableItemVariant);
            $this->dataTransfer->fillEntity($purchasableItemVariantValueData, $purchasableItemVariantValue);
            $purchasableItemVariantValues[] = $purchasableItemVariantValue;
        }

        $event = new PurchasableItemVariantCreatedEvent($creationData, $purchasableItemVariant, $purchasableItemVariantValues);
        $this->eventDispatcher->dispatch($event, PurchasableItemVariantCreatedEvent::NAME);
    }
}