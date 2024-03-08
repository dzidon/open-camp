<?php

namespace App\Model\Service\Application;

use App\Model\Entity\Application;
use App\Model\Event\User\ApplicationPurchasableItemInstance\ApplicationPurchasableItemInstanceDeleteEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @inheritDoc
 */
class ApplicationOverflownPurchasableItemsRemover implements ApplicationOverflownPurchasableItemsRemoverInterface
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @inheritDoc
     */
    public function removeOverflownPurchasableItems(Application $application): void
    {
        $applicationPurchasableItems = $application->getApplicationPurchasableItems();

        if ($application->isPurchasableItemsIndividualMode())
        {
            return;
        }

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
}