<?php

namespace App\Model\EventSubscriber\Admin\CampDatePurchasableItem;

use App\Model\Event\Admin\CampDatePurchasableItem\CampDatePurchasableItemUpdateEvent;
use App\Model\Repository\CampDatePurchasableItemRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class CampDatePurchasableItemUpdateSubscriber
{
    private CampDatePurchasableItemRepositoryInterface $repository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(CampDatePurchasableItemRepositoryInterface $repository,
                                DataTransferRegistryInterface              $dataTransfer)
    {
        $this->repository = $repository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: CampDatePurchasableItemUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(CampDatePurchasableItemUpdateEvent $event): void
    {
        $data = $event->getCampDatePurchasableItemData();
        $entity = $event->getCampDatePurchasableItem();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: CampDatePurchasableItemUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(CampDatePurchasableItemUpdateEvent $event): void
    {
        $entity = $event->getCampDatePurchasableItem();
        $flush = $event->isFlush();
        $this->repository->saveCampDatePurchasableItem($entity, $flush);
    }
}