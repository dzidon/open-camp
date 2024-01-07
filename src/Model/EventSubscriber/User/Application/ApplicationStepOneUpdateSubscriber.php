<?php

namespace App\Model\EventSubscriber\User\Application;

use App\Model\Event\User\Application\ApplicationStepOneUpdateEvent;
use App\Model\Event\User\ApplicationPurchasableItemInstance\ApplicationPurchasableItemInstanceDeleteEvent;
use App\Model\Repository\ApplicationRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ApplicationStepOneUpdateSubscriber
{
    private ApplicationRepositoryInterface $applicationRepository;

    private DataTransferRegistryInterface $dataTransfer;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(ApplicationRepositoryInterface $applicationRepository,
                                DataTransferRegistryInterface  $dataTransfer,
                                EventDispatcherInterface       $eventDispatcher)
    {
        $this->applicationRepository = $applicationRepository;
        $this->dataTransfer = $dataTransfer;
        $this->eventDispatcher = $eventDispatcher;
    }

    #[AsEventListener(event: ApplicationStepOneUpdateEvent::NAME, priority: 300)]
    public function onUpdateFillEntity(ApplicationStepOneUpdateEvent $event): void
    {
        $data = $event->getApplicationStepOneData();
        $entity = $event->getApplication();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: ApplicationStepOneUpdateEvent::NAME, priority: 200)]
    public function onUpdateFixOverflownPurchasableItems(ApplicationStepOneUpdateEvent $event): void
    {
        $application = $event->getApplication();
        $applicationPurchasableItems = $application->getApplicationPurchasableItems();

        foreach ($applicationPurchasableItems as $applicationPurchasableItem)
        {
            if ($applicationPurchasableItem->isGlobal())
            {
                continue;
            }

            $calculatedMaxAmount = $applicationPurchasableItem->getCalculatedMaxAmount();
            $totalAmount = $applicationPurchasableItem->getInstancesTotalAmount();

            if ($totalAmount > $calculatedMaxAmount)
            {
                foreach ($applicationPurchasableItem->getApplicationPurchasableItemInstances() as $applicationPurchasableItemInstance)
                {
                    $event = new ApplicationPurchasableItemInstanceDeleteEvent($applicationPurchasableItemInstance);
                    $event->setIsFlush(false);
                    $this->eventDispatcher->dispatch($event, $event::NAME);
                }
            }
        }
    }

    #[AsEventListener(event: ApplicationStepOneUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(ApplicationStepOneUpdateEvent $event): void
    {
        $application = $event->getApplication();
        $isFlush = $event->isFlush();
        $this->applicationRepository->saveApplication($application, $isFlush);
    }
}