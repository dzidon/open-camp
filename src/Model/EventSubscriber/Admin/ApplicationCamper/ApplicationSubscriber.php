<?php

namespace App\Model\EventSubscriber\Admin\ApplicationCamper;

use App\Model\Event\Admin\ApplicationCamper\ApplicationCamperCreateEvent;
use App\Model\Event\Admin\ApplicationCamper\ApplicationCamperDeleteEvent;
use App\Model\Event\Admin\ApplicationCamper\ApplicationCamperUpdateEvent;
use App\Model\Service\Application\ApplicationOverflownPurchasableItemsRemoverInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationSubscriber
{
    private ApplicationOverflownPurchasableItemsRemoverInterface $applicationOverflownPurchasableItemsRemover;

    public function __construct(ApplicationOverflownPurchasableItemsRemoverInterface $applicationOverflownPurchasableItemsRemover)
    {
        $this->applicationOverflownPurchasableItemsRemover = $applicationOverflownPurchasableItemsRemover;
    }

    #[AsEventListener(event: ApplicationCamperCreateEvent::NAME, priority: 400)]
    #[AsEventListener(event: ApplicationCamperUpdateEvent::NAME, priority: 400)]
    #[AsEventListener(event: ApplicationCamperDeleteEvent::NAME, priority: 400)]
    public function onUpdateFixOverflownPurchasableItems(
        ApplicationCamperCreateEvent|ApplicationCamperUpdateEvent|ApplicationCamperDeleteEvent $event
    ): void {
        $applicationCamper = $event->getApplicationCamper();
        $application = $applicationCamper->getApplication();
        $this->applicationOverflownPurchasableItemsRemover->removeOverflownPurchasableItems($application);
    }

    #[AsEventListener(event: ApplicationCamperCreateEvent::NAME, priority: 300)]
    #[AsEventListener(event: ApplicationCamperUpdateEvent::NAME, priority: 300)]
    #[AsEventListener(event: ApplicationCamperDeleteEvent::NAME, priority: 300)]
    public function onUpdateResetSiblingsDiscountIfInvalid(
        ApplicationCamperCreateEvent|ApplicationCamperUpdateEvent|ApplicationCamperDeleteEvent $event
    ): void {
        $applicationCamper = $event->getApplicationCamper();
        $application = $applicationCamper->getApplication();
        $application->resetSiblingsDiscountIfIntervalNotEligibleForNumberOfCampers();
    }

    #[AsEventListener(event: ApplicationCamperCreateEvent::NAME, priority: 200)]
    #[AsEventListener(event: ApplicationCamperUpdateEvent::NAME, priority: 200)]
    #[AsEventListener(event: ApplicationCamperDeleteEvent::NAME, priority: 200)]
    public function onCreateCacheFullPrice(
        ApplicationCamperCreateEvent|ApplicationCamperUpdateEvent|ApplicationCamperDeleteEvent $event
    ): void {
        $applicationCamper = $event->getApplicationCamper();
        $application = $applicationCamper->getApplication();
        $application->cacheFullPrice();
    }
}