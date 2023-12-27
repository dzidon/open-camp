<?php

namespace App\Model\EventSubscriber\Admin\CampDatePurchasableItem;

use App\Model\Entity\CampDatePurchasableItem;
use App\Model\Event\Admin\CampDatePurchasableItem\CampDatePurchasableItemCreateEvent;
use App\Model\Repository\CampDatePurchasableItemRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class CampDatePurchasableItemCreateSubscriber
{
    private DataTransferRegistryInterface $dataTransfer;

    private CampDatePurchasableItemRepositoryInterface $repository;

    public function __construct(DataTransferRegistryInterface $dataTransfer, CampDatePurchasableItemRepositoryInterface $repository)
    {
        $this->dataTransfer = $dataTransfer;
        $this->repository = $repository;
    }

    #[AsEventListener(event: CampDatePurchasableItemCreateEvent::NAME, priority: 200)]
    public function onCreateFillEntity(CampDatePurchasableItemCreateEvent $event): void
    {
        $campDate = $event->getCampDate();
        $data = $event->getCampDatePurchasableItemData();
        $entity = new CampDatePurchasableItem($campDate, $data->getPurchasableItem(), $data->getPriority());
        $this->dataTransfer->fillEntity($data, $entity);
        $event->setCampDatePurchasableItem($entity);
    }

    #[AsEventListener(event: CampDatePurchasableItemCreateEvent::NAME, priority: 100)]
    public function onCreateSaveEntity(CampDatePurchasableItemCreateEvent $event): void
    {
        $entity = $event->getCampDatePurchasableItem();
        $flush = $event->isFlush();
        $this->repository->saveCampDatePurchasableItem($entity, $flush);
    }
}