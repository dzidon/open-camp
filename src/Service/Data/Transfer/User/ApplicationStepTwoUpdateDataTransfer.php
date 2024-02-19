<?php

namespace App\Service\Data\Transfer\User;

use App\Library\Data\User\ApplicationCamperPurchasableItemsData;
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
        /** @var ApplicationStepTwoUpdateData $applicationStepTwoUpdateData */
        /** @var Application $application */
        $applicationStepTwoUpdateData = $data;
        $application = $entity;
        $isPurchasableItemsIndividualMode = $application->isPurchasableItemsIndividualMode();

        $applicationStepTwoUpdateData->setPaymentMethod($application->getPaymentMethod());
        $applicationStepTwoUpdateData->setNote($application->getNote());
        $applicationStepTwoUpdateData->setCustomerChannel($application->getCustomerChannel());
        $applicationStepTwoUpdateData->setCustomerChannelOther($application->getCustomerChannelOther());

        $discountSiblingsIntervalFrom = $application->getDiscountSiblingsIntervalFrom();
        $discountSiblingsIntervalTo = $application->getDiscountSiblingsIntervalTo();

        if ($discountSiblingsIntervalFrom === null && $discountSiblingsIntervalTo === null)
        {
            $applicationStepTwoUpdateData->setDiscountSiblingsInterval(false);
        }
        else
        {
            $applicationStepTwoUpdateData->setDiscountSiblingsInterval([
                'from' => $discountSiblingsIntervalFrom,
                'to'   => $discountSiblingsIntervalTo,
            ]);
        }

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
        /** @var ApplicationStepTwoUpdateData $applicationStepTwoUpdateData */
        /** @var Application $application */
        $applicationStepTwoUpdateData = $data;
        $application = $entity;

        $application->setPaymentMethod($applicationStepTwoUpdateData->getPaymentMethod());
        $application->setNote($applicationStepTwoUpdateData->getNote());
        $application->setCustomerChannel(
            $applicationStepTwoUpdateData->getCustomerChannel(),
            $applicationStepTwoUpdateData->getCustomerChannelOther()
        );

        $discountSiblingsInterval = $applicationStepTwoUpdateData->getDiscountSiblingsInterval();

        if ($discountSiblingsInterval === false)
        {
            $application->setDiscountSiblingsInterval(null, null);
        }
        else
        {
            $discountSiblingsIntervalFrom = $discountSiblingsInterval['from'];
            $discountSiblingsIntervalTo = $discountSiblingsInterval['to'];
            $application->setDiscountSiblingsInterval($discountSiblingsIntervalFrom, $discountSiblingsIntervalTo);
        }

        $applicationPurchasableItemsData = $applicationStepTwoUpdateData->getApplicationPurchasableItemsData();

        foreach ($applicationPurchasableItemsData as $applicationPurchasableItemData)
        {
            $applicationPurchasableItem = $applicationPurchasableItemData->getApplicationPurchasableItem();
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
                $event = new ApplicationPurchasableItemUpdateEvent($applicationPurchasableItemData, $applicationPurchasableItem);
                $event->setIsFlush(false);
                $this->eventDispatcher->dispatch($event, $event::NAME);
            }
        }
    }
}