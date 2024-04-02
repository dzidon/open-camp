<?php

namespace App\Service\Data\Transfer\User;

use App\Library\Data\User\ApplicationPurchasableItemData;
use App\Library\Data\User\ApplicationCamperPurchasableItemsData;
use App\Library\Data\User\ApplicationStepTwoData;
use App\Model\Entity\Application;
use App\Model\Event\User\ApplicationPurchasableItem\ApplicationPurchasableItemUpdateEvent;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Data\Transfer\DataTransferInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Transfers data from {@link ApplicationStepTwoData} to {@link Application} and vice versa.
 */
class ApplicationStepTwoDataTransfer implements DataTransferInterface
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
        return $data instanceof ApplicationStepTwoData && $entity instanceof Application;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var ApplicationStepTwoData $applicationStepTwoUpdateData */
        /** @var Application $application */
        $applicationStepTwoUpdateData = $data;
        $application = $entity;
        $isPurchasableItemsIndividualMode = $application->isPurchasableItemsIndividualMode();

        $applicationStepTwoUpdateData->setPaymentMethod($application->getPaymentMethod());
        $applicationStepTwoUpdateData->setNote($application->getNote());
        $applicationStepTwoUpdateData->setCustomerChannel($application->getCustomerChannel());
        $applicationStepTwoUpdateData->setCustomerChannelOther($application->getCustomerChannelOther());

        $applicationDiscountsData = $applicationStepTwoUpdateData->getApplicationDiscountsData();
        $this->dataTransfer->fillData($applicationDiscountsData, $application);

        foreach ($application->getApplicationPurchasableItems() as $applicationPurchasableItem)
        {
            $isGlobal = $applicationPurchasableItem->isGlobal();

            if ($isPurchasableItemsIndividualMode && !$isGlobal)
            {
                continue;
            }

            $applicationPurchasableItemData = new ApplicationPurchasableItemData($applicationPurchasableItem);
            $this->dataTransfer->fillData($applicationPurchasableItemData, $applicationPurchasableItem);
            $applicationStepTwoUpdateData->addApplicationPurchasableItemsDatum($applicationPurchasableItemData);
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
                    $applicationStepTwoUpdateData->addApplicationCamperPurchasableItemsDatum($applicationCamperPurchasableItemsData);
                }
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var ApplicationStepTwoData $applicationStepTwoUpdateData */
        /** @var Application $application */
        $applicationStepTwoUpdateData = $data;
        $application = $entity;

        $application->setPaymentMethod($applicationStepTwoUpdateData->getPaymentMethod());
        $application->setNote($applicationStepTwoUpdateData->getNote());
        $application->setCustomerChannel(
            $applicationStepTwoUpdateData->getCustomerChannel(),
            $applicationStepTwoUpdateData->getCustomerChannelOther()
        );

        $applicationDiscountsData = $applicationStepTwoUpdateData->getApplicationDiscountsData();
        $this->dataTransfer->fillEntity($applicationDiscountsData, $application);

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