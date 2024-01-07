<?php

namespace App\Service\Data\Transfer\User;

use App\Library\Data\User\ApplicationPurchasableItemData;
use App\Library\Data\User\ApplicationStepTwoUpdateData;
use App\Model\Entity\Application;
use App\Model\Event\User\ApplicationPurchasableItem\ApplicationPurchasableItemUpdateEvent;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Data\Transfer\DataTransferInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Transfers data from {@link ApplicationStepTwoUpdateData} to {@link Application} and vice versa.
 */
class ApplicationStepTwoUpdateDataTransfer implements DataTransferInterface
{
    private DataTransferRegistryInterface $dataTransfer;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(DataTransferRegistryInterface $dataTransfer, EventDispatcherInterface $eventDispatcher)
    {
        $this->dataTransfer = $dataTransfer;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @inheritDoc
     */
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof ApplicationStepTwoUpdateData && $entity instanceof Application;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var ApplicationStepTwoUpdateData $applicationPurchasableItemsData */
        /** @var Application $application */
        $applicationPurchasableItemsData = $data;
        $application = $entity;

        foreach ($application->getApplicationPurchasableItems() as $applicationPurchasableItem)
        {
            $applicationPurchasableItemData = new ApplicationPurchasableItemData($applicationPurchasableItem);
            $this->dataTransfer->fillData($applicationPurchasableItemData, $applicationPurchasableItem);
            $applicationPurchasableItemsData->addApplicationPurchasableItemsDatum($applicationPurchasableItemData);
        }
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var ApplicationStepTwoUpdateData $applicationPurchasableItemsData */
        /** @var Application $application */
        $applicationPurchasableItemsData = $data;
        $application = $entity;

        $applicationPurchasableItems = $application->getApplicationPurchasableItems();

        foreach ($applicationPurchasableItemsData->getApplicationPurchasableItemsData() as $index => $applicationPurchasableItemData)
        {
            if (!array_key_exists($index, $applicationPurchasableItems))
            {
                continue;
            }

            $applicationPurchasableItem = $applicationPurchasableItems[$index];
            $event = new ApplicationPurchasableItemUpdateEvent($applicationPurchasableItemData, $applicationPurchasableItem);
            $event->setIsFlush(false);
            $this->eventDispatcher->dispatch($event, $event::NAME);
        }
    }
}