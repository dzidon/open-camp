<?php

namespace App\Model\EventSubscriber\Admin\ApplicationPurchasableItemInstance;

use App\Model\Event\Admin\ApplicationPurchasableItemInstance\ApplicationPurchasableItemInstanceVariantUpdateEvent;
use App\Model\Repository\ApplicationPurchasableItemInstanceRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationPurchasableItemInstanceVariantUpdateSubscriber
{
    private ApplicationPurchasableItemInstanceRepositoryInterface $repository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(ApplicationPurchasableItemInstanceRepositoryInterface $repository,
                                DataTransferRegistryInterface                         $dataTransfer)
    {
        $this->repository = $repository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: ApplicationPurchasableItemInstanceVariantUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(ApplicationPurchasableItemInstanceVariantUpdateEvent $event): void
    {
        $data = $event->getApplicationPurchasableItemVariantData();
        $entity = $event->getApplicationPurchasableItemInstance();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: ApplicationPurchasableItemInstanceVariantUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(ApplicationPurchasableItemInstanceVariantUpdateEvent $event): void
    {
        $entity = $event->getApplicationPurchasableItemInstance();
        $isFlush = $event->isFlush();
        $this->repository->saveApplicationPurchasableItemInstance($entity, $isFlush);
    }
}