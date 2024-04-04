<?php

namespace App\Model\EventSubscriber\Admin\ApplicationCamper;

use App\Model\Event\Admin\ApplicationCamper\ApplicationCamperCreateEvent;
use App\Model\Event\Admin\ApplicationCamper\ApplicationCamperDeleteEvent;
use App\Model\Event\Admin\ApplicationCamper\ApplicationCamperUpdateEvent;
use App\Model\Event\Admin\ApplicationPurchasableItemInstance\ApplicationPurchasableItemInstanceDeleteEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ApplicationSubscriber
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    #[AsEventListener(event: ApplicationCamperCreateEvent::NAME, priority: 400)]
    #[AsEventListener(event: ApplicationCamperUpdateEvent::NAME, priority: 400)]
    #[AsEventListener(event: ApplicationCamperDeleteEvent::NAME, priority: 500)]
    public function fixOverflownPurchasableItems(
        ApplicationCamperCreateEvent|ApplicationCamperUpdateEvent|ApplicationCamperDeleteEvent $event
    ): void {
        $applicationCamper = $event->getApplicationCamper();
        $application = $applicationCamper->getApplication();
        $overflownPurchasableItems = $application->getOverflownPurchasableItems();

        foreach ($overflownPurchasableItems as $overflownPurchasableItem)
        {
            foreach ($overflownPurchasableItem->getApplicationPurchasableItemInstances() as $instance)
            {
                $instanceDeleteEvent = new ApplicationPurchasableItemInstanceDeleteEvent($instance);
                $instanceDeleteEvent->setIsFlush(false);
                $this->eventDispatcher->dispatch($instanceDeleteEvent, $instanceDeleteEvent::NAME);
            }
        }
    }

    #[AsEventListener(event: ApplicationCamperCreateEvent::NAME, priority: 300)]
    #[AsEventListener(event: ApplicationCamperUpdateEvent::NAME, priority: 300)]
    #[AsEventListener(event: ApplicationCamperDeleteEvent::NAME, priority: 400)]
    public function resetSiblingsDiscountIfInvalid(
        ApplicationCamperCreateEvent|ApplicationCamperUpdateEvent|ApplicationCamperDeleteEvent $event
    ): void {
        $applicationCamper = $event->getApplicationCamper();
        $application = $applicationCamper->getApplication();
        $application->resetSiblingsDiscountIfIntervalNotEligibleForNumberOfCampers();
    }

    #[AsEventListener(event: ApplicationCamperCreateEvent::NAME, priority: 200)]
    #[AsEventListener(event: ApplicationCamperUpdateEvent::NAME, priority: 200)]
    #[AsEventListener(event: ApplicationCamperDeleteEvent::NAME, priority: 200)]
    public function cacheAllFullPrices(
        ApplicationCamperCreateEvent|ApplicationCamperUpdateEvent|ApplicationCamperDeleteEvent $event
    ): void {
        $applicationCamper = $event->getApplicationCamper();
        $application = $applicationCamper->getApplication();
        $application->cacheAllFullPrices();
    }
}