<?php

namespace App\Model\EventSubscriber\Admin\PurchasableItemVariantValue;

use App\Model\Entity\PurchasableItemVariantValue;
use App\Model\Event\Admin\PurchasableItemVariantValue\PurchasableItemVariantValueCreatedEvent;
use App\Model\Event\Admin\PurchasableItemVariantValue\PurchasableItemVariantValueCreateEvent;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PurchasableItemVariantValueCreateSubscriber
{
    private DataTransferRegistryInterface $dataTransfer;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(DataTransferRegistryInterface $dataTransfer, EventDispatcherInterface $eventDispatcher)
    {
        $this->dataTransfer = $dataTransfer;
        $this->eventDispatcher = $eventDispatcher;
    }

    #[AsEventListener(event: PurchasableItemVariantValueCreateEvent::NAME)]
    public function onCreateFillEntity(PurchasableItemVariantValueCreateEvent $event): void
    {
        $data = $event->getPurchasableItemVariantValueData();
        $entity = new PurchasableItemVariantValue($data->getName(), $data->getPriority(), $data->getPurchasableItemVariant());
        $this->dataTransfer->fillEntity($data, $entity);

        $event = new PurchasableItemVariantValueCreatedEvent($data, $entity);
        $this->eventDispatcher->dispatch($event, PurchasableItemVariantValueCreatedEvent::NAME);
    }
}