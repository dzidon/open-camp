<?php

namespace App\Model\EventSubscriber\User\Application;

use App\Model\Event\User\Application\ApplicationStepTwoUpdateEvent;
use App\Model\Repository\ApplicationRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationStepTwoUpdateSubscriber
{
    private ApplicationRepositoryInterface $applicationRepository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(ApplicationRepositoryInterface $applicationRepository,
                                DataTransferRegistryInterface  $dataTransfer)
    {
        $this->applicationRepository = $applicationRepository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: ApplicationStepTwoUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(ApplicationStepTwoUpdateEvent $event): void
    {
        $data = $event->getApplicationPurchasableItemsData();
        $entity = $event->getApplication();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: ApplicationStepTwoUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(ApplicationStepTwoUpdateEvent $event): void
    {
        $entity = $event->getApplication();
        $isFlush = $event->isFlush();
        $this->applicationRepository->saveApplication($entity, $isFlush);
    }
}