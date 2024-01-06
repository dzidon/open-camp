<?php

namespace App\Model\EventSubscriber\User\ApplicationPurchasableItem;

use App\Model\Event\User\ApplicationPurchasableItem\ApplicationPurchasableItemCreateEvent;
use App\Model\Repository\ApplicationPurchasableItemRepositoryInterface;
use App\Model\Service\ApplicationPurchasableItem\ApplicationPurchasableItemFactoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationPurchasableItemCreateSubscriber
{
    private ApplicationPurchasableItemFactoryInterface $applicationPurchasableItemFactory;

    private ApplicationPurchasableItemRepositoryInterface $repository;

    public function __construct(ApplicationPurchasableItemFactoryInterface    $applicationPurchasableItemFactory,
                                ApplicationPurchasableItemRepositoryInterface $repository)
    {
        $this->applicationPurchasableItemFactory = $applicationPurchasableItemFactory;
        $this->repository = $repository;
    }

    #[AsEventListener(event: ApplicationPurchasableItemCreateEvent::NAME, priority: 200)]
    public function onCreateInstantiateEntities(ApplicationPurchasableItemCreateEvent $event): void
    {
        $campDatePurchasableItem = $event->getCampDatePurchasableItem();
        $application = $event->getApplication();
        $applicationPurchasableItem = $this->applicationPurchasableItemFactory->createApplicationPurchasableItem($campDatePurchasableItem, $application);
        $event->setApplicationPurchasableItem($applicationPurchasableItem);
    }

    #[AsEventListener(event: ApplicationPurchasableItemCreateEvent::NAME, priority: 100)]
    public function onCreateSaveEntities(ApplicationPurchasableItemCreateEvent $event): void
    {
        $applicationPurchasableItem = $event->getApplicationPurchasableItem();
        $isFlush = $event->isFlush();
        $this->repository->saveApplicationPurchasableItem($applicationPurchasableItem, $isFlush);
    }
}