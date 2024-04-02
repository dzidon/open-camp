<?php

namespace App\Model\EventSubscriber\Admin\ApplicationPurchasableItemInstance;

use App\Model\Event\Admin\ApplicationPurchasableItemInstance\ApplicationPurchasableItemInstanceUpdateEvent;
use App\Model\Repository\ApplicationPurchasableItemInstanceRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationPurchasableItemInstanceUpdateSubscriber
{
    private ApplicationPurchasableItemInstanceRepositoryInterface $repository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(ApplicationPurchasableItemInstanceRepositoryInterface $repository,
                                DataTransferRegistryInterface                         $dataTransfer)
    {
        $this->repository = $repository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: ApplicationPurchasableItemInstanceUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(ApplicationPurchasableItemInstanceUpdateEvent $event): void
    {
        $data = $event->getApplicationPurchasableItemInstanceData();
        $entity = $event->getApplicationPurchasableItemInstance();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: ApplicationPurchasableItemInstanceUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(ApplicationPurchasableItemInstanceUpdateEvent $event): void
    {
        $entity = $event->getApplicationPurchasableItemInstance();
        $isFlush = $event->isFlush();
        $this->repository->saveApplicationPurchasableItemInstance($entity, $isFlush);
    }
}