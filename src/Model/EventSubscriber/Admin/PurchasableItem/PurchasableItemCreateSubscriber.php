<?php

namespace App\Model\EventSubscriber\Admin\PurchasableItem;

use App\Model\Entity\PurchasableItem;
use App\Model\Event\Admin\PurchasableItem\PurchasableItemCreatedEvent;
use App\Model\Event\Admin\PurchasableItem\PurchasableItemCreateEvent;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PurchasableItemCreateSubscriber
{
    private DataTransferRegistryInterface $dataTransfer;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(DataTransferRegistryInterface $dataTransfer, EventDispatcherInterface $eventDispatcher)
    {
        $this->dataTransfer = $dataTransfer;
        $this->eventDispatcher = $eventDispatcher;
    }

    #[AsEventListener(event: PurchasableItemCreateEvent::NAME)]
    public function onCreateFillEntity(PurchasableItemCreateEvent $event): void
    {
        $data = $event->getPurchasableItemData();
        $entity = new PurchasableItem($data->getName(), $data->getLabel(), $data->getPrice(), $data->getMaxAmount());
        $this->dataTransfer->fillEntity($data, $entity);

        $event = new PurchasableItemCreatedEvent($data, $entity);
        $this->eventDispatcher->dispatch($event, PurchasableItemCreatedEvent::NAME);
    }
}