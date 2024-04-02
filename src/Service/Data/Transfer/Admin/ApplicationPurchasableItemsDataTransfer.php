<?php

namespace App\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\ApplicationPurchasableItemsData;
use App\Library\Data\Admin\ApplicationPurchasableItemData;
use App\Library\Data\Admin\ApplicationCamperPurchasableItemsData;
use App\Model\Entity\Application;
use App\Model\Event\Admin\ApplicationPurchasableItem\ApplicationPurchasableItemUpdateEvent;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Data\Transfer\DataTransferInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Transfers data from {@link ApplicationPurchasableItemsData} to {@link Application} and vice versa.
 */
class ApplicationPurchasableItemsDataTransfer implements DataTransferInterface
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
        return $data instanceof ApplicationPurchasableItemsData && $entity instanceof Application;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var ApplicationPurchasableItemsData $applicationPurchasableItemsData */
        /** @var Application $application */
        $applicationPurchasableItemsData = $data;
        $application = $entity;
        $isPurchasableItemsIndividualMode = $application->isPurchasableItemsIndividualMode();

        foreach ($application->getApplicationPurchasableItems() as $applicationPurchasableItem)
        {
            $isGlobal = $applicationPurchasableItem->isGlobal();

            if ($isPurchasableItemsIndividualMode && !$isGlobal)
            {
                continue;
            }

            $applicationPurchasableItemData = new ApplicationPurchasableItemData($applicationPurchasableItem);
            $this->dataTransfer->fillData($applicationPurchasableItemData, $applicationPurchasableItem);
            $applicationPurchasableItemsData->addApplicationPurchasableItemsDatum($applicationPurchasableItemData);
        }

        if ($isPurchasableItemsIndividualMode)
        {
            foreach ($application->getApplicationCampers() as $applicationCamper)
            {
                $skip = true;
                $applicationCamperPurchasableItemsData = new ApplicationCamperPurchasableItemsData();

                foreach ($application->getApplicationPurchasableItems() as $applicationPurchasableItem)
                {
                    $isGlobal = $applicationPurchasableItem->isGlobal();

                    if ($isGlobal)
                    {
                        continue;
                    }

                    $skip = false;
                    $applicationPurchasableItemData = new ApplicationPurchasableItemData($applicationPurchasableItem, $applicationCamper);
                    $this->dataTransfer->fillData($applicationPurchasableItemData, $applicationPurchasableItem);
                    $applicationCamperPurchasableItemsData->addApplicationPurchasableItemsDatum($applicationPurchasableItemData);
                }

                if (!$skip)
                {
                    $applicationPurchasableItemsData->addApplicationCamperPurchasableItemsDatum($applicationCamperPurchasableItemsData);
                }
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var ApplicationPurchasableItemsData $applicationStepTwoUpdateData */
        /** @var Application $application */
        $applicationStepTwoUpdateData = $data;
        $application = $entity;

        $applicationPurchasableItemsData = $applicationStepTwoUpdateData->getApplicationPurchasableItemsData();

        foreach ($applicationPurchasableItemsData as $applicationPurchasableItemData)
        {
            $applicationPurchasableItem = $applicationPurchasableItemData->getApplicationPurchasableItem();

            if (!in_array($applicationPurchasableItem, $application->getApplicationPurchasableItems()))
            {
                continue;
            }

            $event = new ApplicationPurchasableItemUpdateEvent($applicationPurchasableItemData, $applicationPurchasableItem);
            $event->setIsFlush(false);
            $this->eventDispatcher->dispatch($event, $event::NAME);
        }

        $applicationCamperPurchasableItemsData = $applicationStepTwoUpdateData->getApplicationCamperPurchasableItemsData();

        foreach ($applicationCamperPurchasableItemsData as $applicationCamperPurchasableItemsDatum)
        {
            $applicationPurchasableItemsData = $applicationCamperPurchasableItemsDatum->getApplicationPurchasableItemsData();

            foreach ($applicationPurchasableItemsData as $applicationPurchasableItemData)
            {
                $applicationPurchasableItem = $applicationPurchasableItemData->getApplicationPurchasableItem();

                if (!in_array($applicationPurchasableItem, $application->getApplicationPurchasableItems()))
                {
                    continue;
                }

                $event = new ApplicationPurchasableItemUpdateEvent($applicationPurchasableItemData, $applicationPurchasableItem);
                $event->setIsFlush(false);
                $this->eventDispatcher->dispatch($event, $event::NAME);
            }
        }
    }
}