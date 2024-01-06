<?php

namespace App\Model\EventSubscriber\User\ApplicationPurchasableItemInstance;

use App\Model\Event\User\ApplicationPurchasableItemInstance\ApplicationPurchasableItemInstanceUpdateEvent;
use App\Model\Repository\ApplicationPurchasableItemInstanceRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationPurchasableItemInstanceUpdateSubscriber
{
    private ApplicationPurchasableItemInstanceRepositoryInterface $ApplicationPurchasableItemInstanceRepository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(ApplicationPurchasableItemInstanceRepositoryInterface $ApplicationPurchasableItemInstanceRepository,
                                DataTransferRegistryInterface                         $dataTransfer)
    {
        $this->ApplicationPurchasableItemInstanceRepository = $ApplicationPurchasableItemInstanceRepository;
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
        $this->ApplicationPurchasableItemInstanceRepository->saveApplicationPurchasableItemInstance($entity, $isFlush);
    }
}