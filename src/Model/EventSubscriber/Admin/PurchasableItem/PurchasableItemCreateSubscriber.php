<?php

namespace App\Model\EventSubscriber\Admin\PurchasableItem;

use App\Model\Entity\PurchasableItem;
use App\Model\Event\Admin\PurchasableItem\PurchasableItemCreateEvent;
use App\Model\Repository\PurchasableItemRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class PurchasableItemCreateSubscriber
{
    private DataTransferRegistryInterface $dataTransfer;

    private PurchasableItemRepositoryInterface $repository;

    public function __construct(DataTransferRegistryInterface $dataTransfer, PurchasableItemRepositoryInterface $repository)
    {
        $this->dataTransfer = $dataTransfer;
        $this->repository = $repository;
    }

    #[AsEventListener(event: PurchasableItemCreateEvent::NAME, priority: 300)]
    public function onCreateFillEntity(PurchasableItemCreateEvent $event): void
    {
        $data = $event->getPurchasableItemData();
        $entity = new PurchasableItem($data->getName(), $data->getLabel(), $data->getPrice(), $data->getMaxAmount());
        $this->dataTransfer->fillEntity($data, $entity);
        $event->setPurchasableItem($entity);
    }

    #[AsEventListener(event: PurchasableItemCreateEvent::NAME, priority: 100)]
    public function onCreateSaveEntity(PurchasableItemCreateEvent $event): void
    {
        $entity = $event->getPurchasableItem();
        $isFlush = $event->isFlush();
        $this->repository->savePurchasableItem($entity, $isFlush);
    }
}