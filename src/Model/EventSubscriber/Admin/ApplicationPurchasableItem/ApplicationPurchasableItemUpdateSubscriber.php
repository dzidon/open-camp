<?php

namespace App\Model\EventSubscriber\Admin\ApplicationPurchasableItem;

use App\Model\Event\Admin\ApplicationPurchasableItem\ApplicationPurchasableItemUpdateEvent;
use App\Model\Repository\ApplicationPurchasableItemRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationPurchasableItemUpdateSubscriber
{
    private ApplicationPurchasableItemRepositoryInterface $applicationPurchasableItemRepository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(ApplicationPurchasableItemRepositoryInterface $applicationPurchasableItemRepository,
                                DataTransferRegistryInterface                 $dataTransfer)
    {
        $this->applicationPurchasableItemRepository = $applicationPurchasableItemRepository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: ApplicationPurchasableItemUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(ApplicationPurchasableItemUpdateEvent $event): void
    {
        $data = $event->getApplicationPurchasableItemData();
        $entity = $event->getApplicationPurchasableItem();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: ApplicationPurchasableItemUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(ApplicationPurchasableItemUpdateEvent $event): void
    {
        $entity = $event->getApplicationPurchasableItem();
        $isFlush = $event->isFlush();
        $this->applicationPurchasableItemRepository->saveApplicationPurchasableItem($entity, $isFlush);
    }
}