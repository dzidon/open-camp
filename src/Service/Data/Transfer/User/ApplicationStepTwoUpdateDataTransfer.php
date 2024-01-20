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
        /** @var ApplicationStepTwoUpdateData $applicationStepTwoUpdateData */
        /** @var Application $application */
        $applicationStepTwoUpdateData = $data;
        $application = $entity;

        $applicationStepTwoUpdateData->setPaymentMethod($application->getPaymentMethod());
        $applicationStepTwoUpdateData->setNote($application->getNote());
        $applicationStepTwoUpdateData->setCustomerChannel($application->getCustomerChannel());

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
            $applicationPurchasableItemData = new ApplicationPurchasableItemData($applicationPurchasableItem);
            $this->dataTransfer->fillData($applicationPurchasableItemData, $applicationPurchasableItem);
            $applicationStepTwoUpdateData->addApplicationPurchasableItemsDatum($applicationPurchasableItemData);
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
        $application->setCustomerChannel($applicationStepTwoUpdateData->getCustomerChannel());

        $discountSiblingsInterval = $applicationStepTwoUpdateData->getDiscountSiblingsInterval();

        if ($discountSiblingsInterval === false)
        {
            $application->setDiscountSiblingsInterval(null, null);
        }
        else
        {
            $discountSiblingsIntervalFrom = $discountSiblingsInterval[array_key_first($discountSiblingsInterval)];
            $discountSiblingsIntervalTo = $discountSiblingsInterval[array_key_last($discountSiblingsInterval)];
            $application->setDiscountSiblingsInterval($discountSiblingsIntervalFrom, $discountSiblingsIntervalTo);
        }

        $applicationPurchasableItems = $application->getApplicationPurchasableItems();

        foreach ($applicationStepTwoUpdateData->getApplicationPurchasableItemsData() as $index => $applicationPurchasableItemData)
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