<?php

namespace App\Model\EventSubscriber\Admin\ApplicationPurchasableItem;

use App\Model\Event\Admin\ApplicationPurchasableItem\ApplicationPurchasableItemsBulkUpdateEvent;
use App\Model\Repository\ApplicationRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationPurchasableItemsBulkUpdateSubscriber
{
    private ApplicationRepositoryInterface $applicationRepository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(ApplicationRepositoryInterface $applicationRepository,
                                DataTransferRegistryInterface  $dataTransfer)
    {
        $this->applicationRepository = $applicationRepository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: ApplicationPurchasableItemsBulkUpdateEvent::NAME, priority: 300)]
    public function onUpdateFillEntity(ApplicationPurchasableItemsBulkUpdateEvent $event): void
    {
        $data = $event->getApplicationPurchasableItemsData();
        $entity = $event->getApplication();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: ApplicationPurchasableItemsBulkUpdateEvent::NAME, priority: 200)]
    public function onUpdateCacheFullPrice(ApplicationPurchasableItemsBulkUpdateEvent $event): void
    {
        $application = $event->getApplication();
        $application->cacheFullPrice();
    }

    #[AsEventListener(event: ApplicationPurchasableItemsBulkUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(ApplicationPurchasableItemsBulkUpdateEvent $event): void
    {
        $entity = $event->getApplication();
        $isFlush = $event->isFlush();
        $this->applicationRepository->saveApplication($entity, $isFlush);
    }
}